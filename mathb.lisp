;;;; MathB - A Mathematics Pastebin that Powers MathB.in
;;;; ===================================================


(ql:quickload "hunchentoot")
(require "uiop")


;;; Special Modes
;;; -------------

(defvar *log-mode* t
  "Write logs iff true.")

(defvar *main-mode* t
  "Run main function iff true.")


;;; General Definitions
;;; -------------------

(defun universal-time-string (universal-time-seconds)
  "Return given universal time in yyyy-mm-dd HH:MM:SS +0000 format."
  (multiple-value-bind (sec min hour date month year)
      (decode-universal-time universal-time-seconds 0)
    (format nil "~4,'0d-~2,'0d-~2,'0d ~2,'0d:~2,'0d:~2,'0d +0000"
            year month date hour min sec)))

(defun current-utc-time-string ()
  "Return current UTC date and time in yyyy-mm-dd HH:MM:SS +0000 format."
  (universal-time-string (get-universal-time)))

(defun directory-exists-p (path)
  "Check whether the specified directory exists on the filesystem."
  (uiop:directory-exists-p path))

(defun make-directory (path)
  "Create a new directory along with its parents."
  (ensure-directories-exist path))

(defun read-file (filename)
  "Read file and close the file."
  (uiop:read-file-string filename))

(defun write-file (filename text)
  "Write text to file and close the file."
  (make-directory filename)
  (with-open-file (f filename :direction :output :if-exists :supersede)
    (write-sequence text f)))

(defun real-ip ()
  "Return address of the remote client (not of the local reverse-proxy)."
  (hunchentoot:real-remote-addr))

(defun write-log (fmt &rest args)
  "Log message with specified arguments."
  (when *log-mode*
    (format t "~a - [~a] \"~a ~a\" "
            (real-ip)
            (current-utc-time-string)
            (hunchentoot:request-method*)
            (hunchentoot:request-uri*))
    (apply #'format t fmt args)
    (terpri)))

(defun string-replace (old new string)
  "Replace non-empty old substring in string with new substring."
  (with-output-to-string (s)
    (let* ((next-index 0)
           (match-index))
      (loop
        (setf match-index (search old string :start2 next-index))
        (unless match-index
          (format s "~a" (subseq string next-index))
          (return))
        (format s "~a~a" (subseq string next-index match-index) new)
        (setf next-index (+ match-index (length old)))))))

(defun string-trim-whitespace (s)
  "Trim whitespace from given string."
  (string-trim '(#\space #\tab #\return #\newline) s))

(defun weekday-name (weekday-index)
  "Given an index, return the corresponding day of week."
  (nth weekday-index '("Mon" "Tue" "Wed" "Thu" "Fri" "Sat" "Sun")))

(defun month-name (month-number)
  "Given a number, return the corresponding month."
  (nth month-number '("X" "Jan" "Feb" "Mar" "Apr" "May" "Jun"
                      "Jul" "Aug" "Sep" "Oct" "Nov" "Dec")))

(defun decode-weekday-name (year month date)
  "Given a date, return the day of week."
  (let* ((encoded-time (encode-universal-time 0 0 0 date month year))
         (decoded-week (nth-value 6 (decode-universal-time encoded-time)))
         (weekday-name (weekday-name decoded-week)))
    weekday-name))

(defun simple-date (date-string)
  "Convert yyyy-mm-dd HH:MM:SS TZ to simple date."
  (let* ((year (parse-integer (subseq date-string 0 4)))
         (month (parse-integer (subseq date-string 5 7)))
         (date (parse-integer (subseq date-string 8 10)))
         (hour (parse-integer (subseq date-string 11 13)))
         (minute (parse-integer (subseq date-string 14 16)))
         (month-name (month-name month))
         (weekday-name (decode-weekday-name year month date)))
    (format nil "~a, ~2,'0d ~a ~4,'0d ~2,'0d:~2,'0d GMT"
            weekday-name date month-name year hour minute)))

(defun alist-get (key alist)
  "Given a key, return its value found in the list of parameters."
  (cdr (assoc key alist :test #'string=)))

(defun lock (directory)
  "Acquire lock for specified directory and return t iff successful."
  (let ((lock-dir (merge-pathnames "lock/" directory))
        (failures 0)
        (max-failures 10)
        (status))
    (loop
      (when (nth-value 1 (ensure-directories-exist lock-dir))
        (write-log "Acquired lock")
        (setf status t)
        (return))
      (incf failures)
      (write-log "Could not acquire lock ~a after ~a of ~a attempts"
                 lock-dir failures max-failures)
      (when (= failures max-failures)
        (write-log "Failed to acquire lock")
        (return))
      (sleep 0.2))
    status))

(defun unlock (directory)
  "Release lock for specified directory."
  (uiop:delete-empty-directory (merge-pathnames "lock/" directory))
  (write-log "Released lock"))


;;; Tool Definitions
;;; ----------------

(defvar *data-directory* "/opt/data/mathb/"
  "Directory where post files and data are written to and read from.")

(defun read-options (directory)
  "Read options file."
  (read-from-string (read-file (merge-pathnames "opt.lisp" directory))))

(defun from-post (name)
  "Get the value of a POST parameter."
  (hunchentoot:post-parameter name))

(defun home-request-p (request)
  "Return true iff the home page is requested."
  (and (member (hunchentoot:request-method request) '(:head :get))
       (string= (hunchentoot:script-name request) "/")))

(defun math-request-p (request)
  "Return true iff a mathematics post is requested."
  (and (member (hunchentoot:request-method request) '(:head :get))
       (> (length (hunchentoot:script-name request)) 1)
       (every #'digit-char-p (subseq (hunchentoot:script-name request) 1))))

(defun post-request-p (request)
  "Return true iff a post submission has been made."
  (and (member (hunchentoot:request-method request) '(:post))
       (or (string= (hunchentoot:script-name request) "/")
           (every #'digit-char-p (subseq (hunchentoot:script-name request) 1)))))

(defun current-year ()
  "Return the current year."
  (nth-value 5 (get-decoded-time)))

(defun slug-to-path (directory slug)
  "Convert a slug to path, e.g., 1234567 to /directory/post/1/1234/1234567.txt"
  (let* ((n (parse-integer slug))
         (short-prefix (floor n 1000000))
         (long-prefix (floor n 1000)))
    (format nil "~apost/~d/~d/~d.txt" directory short-prefix long-prefix n)))

(defun split-text (text)
  "Split text into head and body."
  (let ((delimiter-index (search (format nil "~%~%") text)))
    (values (subseq text 0 (1+ delimiter-index))
            (subseq text (+ 2 delimiter-index)))))

(defun parse-headers (head)
  "Parse head content into an alist of key-value pairs."
  (let ((next-index 0)
        (headers))
    (loop
      (let* ((sep-index (search ":" head :start2 next-index))
             (end-index (search (format nil "~%") head :start2 sep-index))
             (key (subseq head next-index sep-index))
             (val (subseq head (+ 2 sep-index) end-index)))
        (push (cons key val) headers)
        (setf next-index (1+ end-index))
        (when (= next-index (length head))
          (return))))
    headers))

(defun parse-text (text)
  "Parse text into an alist of headers and body."
  (multiple-value-bind (head body) (split-text text)
    (let ((headers (parse-headers head)))
      (values (alist-get "Date" headers)
              (alist-get "Title" headers)
              (alist-get "Name" headers)
              body))))

(defun increment-slug (directory)
  "Acquire data lock, increment slug, and return t iff successful."
  (let ((filename (merge-pathnames "slug.txt" directory))
        (slug))
    (when (lock directory)
      (if (probe-file filename)
          (setf slug (parse-integer (read-file filename)))
          (setf slug 0))
      (write-file filename (format nil "~a~%" (incf slug)))
      (unlock directory)
      (format nil "~a" slug))))

(defun make-text (date title name code)
  "Convert post fields to text to be written to post file."
  (format nil "Date: ~a~%Title: ~a~%Name: ~a~%~%~a~%"
          (string-trim-whitespace date)
          (string-trim-whitespace title)
          (string-trim-whitespace name)
          (string-trim-whitespace code)))

(defun render-html (html date title name code error)
  "Render HTML for a page."
  (setf html (string-replace "{{ date }}" date html))
  (setf html (string-replace "{{ title }}" title html))
  (setf html (string-replace "{{ name }}" name html))
  (setf html (string-replace "{{ code }}" code html))
  (setf html (string-replace "{{ error }}" error html))
  (setf html (string-replace "{{ current-year }}" (current-year) html)))


;;; Post Control
;;; ------------

(defvar *last-post-time* 0
  "The universal-time at which the last post was successfully submitted.")

(defvar *flood-table* (make-hash-table :test #'equal :synchronized t)
  "A map of IP addresses to last time they made a successful post.")

(defmacro set-flood-data (ip current-time last-post-time-var flood-table-var)
  "Update flood control state variables."
  `(progn
     (setf ,last-post-time-var ,current-time)
     (setf (gethash ,ip ,flood-table-var) ,current-time)))

(defun accept-post (ip current-time directory slug title name code)
  "Accept post, update flood data, and redirect client to saved post."
  (write-file (slug-to-path directory slug)
              (make-text (current-utc-time-string) title name code))
  (set-flood-data ip current-time *last-post-time* *flood-table*)
  (write-log "Post ~a written successfully" slug)
  (hunchentoot:redirect (format nil "/~a" slug)))

(defun reject-post (title name code reason)
  "Reject post with an error message."
  (write-log "Post rejected: ~a" reason)
  (render-html (read-file "web/html/mathb.html") "" title name code
               (format nil "<div id=\"error\">ERROR: ~a</div>" reason)))

(defun process-post (ip current-time directory title name code)
  "Process post and either accept it or reject it."
  (let ((slug (increment-slug directory)))
    (if slug
        (accept-post ip current-time directory slug title name code)
        (reject-post title name code "Internal error! Cannot acquire lock!"))))


;;; Validators
;;; ----------

(defun read-only-p (options)
  "Check if read-only mode is enabled."
  (getf options :read-only))

(defun empty-content-p (code)
  "Check if code in the post is empty."
  (string= (string-trim-whitespace code) ""))

(defun dodgy-content-p (options title name code)
  "Check if post content contains banned words."
  (let ((words (getf options :block))
        (text (format nil "~a:~a:~a" title name code)))
    (some (lambda (word) (search word text)) words)))

(defun calc-token (a)
  "Calculate token from given integer."
  (let ((b (mod a 91))
        (c (mod a 87)))
    (+ (* 1000000 a) (* 1000 b) c)))

(defun dodgy-post-p (token)
  "Check if post content contains invalid token."
  (let* ((digits (every #'digit-char-p token))
         (x (if digits (parse-integer token) 0))
         (a (floor x 1000000))
         (xx (calc-token a)))
    (or (< x 123) (/= x xx))))

(defun global-flood-p (options current-time last-post-time)
  "Compute number of seconds before next post will be accepted."
  (let* ((post-interval (getf options :global-post-interval 0))
         (wait-time (- (+ last-post-time post-interval) current-time)))
    (when (plusp wait-time)
      wait-time)))

(defun client-flood-p (options ip current-time flood-table)
  "Compute number of seconds client must wait to avoid client flooding."
  (let ((post-interval (or (getf options :client-post-interval) 0)))
    (maphash #'(lambda (key value)
                 (when (>= current-time (+ value post-interval))
                   (remhash key flood-table)))
             flood-table)
    (write-log "Flood table size is ~a" (hash-table-count flood-table))
    (let* ((last-post-time (gethash ip flood-table 0))
           (wait-time (- (+ last-post-time post-interval) current-time)))
      (when (plusp wait-time)
        wait-time))))

(defun reject-post-p (options ip current-time title name code token)
  "Validate post and return error message if validation fails."
  (let ((max-code-length (getf options :max-code-length 10000))
        (max-title-length (getf options :max-title-length 120))
        (max-name-length (getf options :max-name-length 120))
        (result))
    (cond ((read-only-p options)
           "New posts have been disabled temporarily!")
          ((empty-content-p code)
           "Empty content!")
          ((> (length title) max-title-length)
           (format nil "Title length exceeds ~a characters" max-title-length))
          ((> (length name) max-name-length)
           (format nil "Name length exceeds ~a characters" max-name-length))
          ((> (length code) max-code-length)
           (format nil "Code length exceeds ~a characters" max-code-length))
          ((dodgy-content-p options title name code)
           "Dodgy content!")
          ((dodgy-post-p token)
           "Dodgy post!")
          ((setf result (global-flood-p options current-time *last-post-time*))
           (format nil "~@{~a~}" "Global post interval enforced! Wait for "
                   result " s before submitting again."))
          ((setf result (client-flood-p options ip current-time *flood-table*))
           (format nil "~@{~a~}" "Client post interval enforced! Wait for "
                   result " s before submitting again.")))))


;;; HTTP Request Handlers
;;; ---------------------

(defun home-page ()
  "Return HTML of the home page."
  (render-html (read-file "web/html/mathb.html") "" "" "" "" ""))

(defun math-page (directory)
  "Return page to client."
  (let* ((html (read-file "web/html/mathb.html"))
         (slug (subseq (hunchentoot:script-name*) 1))
         (path (slug-to-path directory slug))
         (exists (probe-file path)))
    (if exists
        (multiple-value-bind (date title name code) (parse-text (read-file path))
          (render-html html (simple-date date) title name code ""))
        (progn (setf (hunchentoot:return-code*) 404) nil))))

(defun post-response (directory)
  "Process submitted post form."
  (let* ((options (read-options directory))
         (ip (real-ip))
         (current-time (get-universal-time))
         (title (or (from-post "title") ""))
         (name (or (from-post "name") ""))
         (code (or (from-post "code") ""))
         (token (or (from-post "token") ""))
         (reject (reject-post-p options ip current-time title name code token)))
    (if reject
        (reject-post title name code reject)
        (process-post ip current-time directory title name code))))

(defun define-handlers ()
  "Define handlers for HTTP requests"
  (let ((directory *data-directory*))
    (hunchentoot:define-easy-handler (home-handler :uri #'home-request-p) ()
      (home-page))
    (hunchentoot:define-easy-handler (math-handler :uri #'math-request-p) ()
      (math-page directory))
    (hunchentoot:define-easy-handler (post-handler :uri #'post-request-p) ()
      (post-response directory))))

(defmethod hunchentoot:acceptor-status-message (acceptor http-status-code &key)
  "Custom error page."
  (let ((html (read-file "web/html/error.html"))
        (reason-phrase (hunchentoot:reason-phrase http-status-code)))
    (setf html (string-replace "{{ status-code }}" http-status-code html))
    (setf html (string-replace "{{ reason-phrase }}" reason-phrase html))
    html))

(defun start-server ()
  "Start HTTP server."
  (let ((acceptor (make-instance 'hunchentoot:easy-acceptor
                                 :address "127.0.0.1" :port 4242)))
    (setf (hunchentoot:acceptor-document-root acceptor) #p"_live/")
    (hunchentoot:start acceptor)))

(defun main ()
  "Set up HTTP request handlers and start server."
  (define-handlers)
  (start-server)
  (sleep most-positive-fixnum))

(when *main-mode*
  (main))
