MathB
=====

MathB is a mathematics pastebin software that powered `MathB.in` from
2012 to 2025.  It is a web-based service meant for sharing snippets of
mathematical text with others on the world wide web.


Contents
--------

* [Features](#features)
* [Quick Start](#quick-start)
* [Custom Directory Paths](#custom-directory-paths)
* [Data Files](#data-files)
* [Runtime Options](#runtime-options)
* [Templates Files](#template-files)
* [Static Files](#static-files)
* [Live Directory](#live-directory)
* [Print](#print)
* [Save PDF](#save-pdf)
* [History](#history)
* [License](#license)
* [Support](#support)
* [Channels](#channels)
* [More](#more)


Features
--------

- Minimalist user interface that has not changed much over a decade.
- Live preview of Markdown and LaTeX content as it is typed.
- Support for mixing Markdown and LaTeX code freely.
- Printing a post to PDF or paper prints only the rendered content.
- All UI elements apart from rendered content are excluded from
  prints.
- No web cookies.
- No web analytics.


Quick Start
-----------

This section explains how to run this project locally.  The steps
assume a macOS, Debian, or Debian-based Linux distribution.  However,
it should be possible to adapt these steps for another operating
system.

 1. Install SBCL and Git.

    On macOS, enter the following command if you have Homebrew:

    ```sh
    brew install sbcl git
    ```

    On Debian, Ubuntu, or another Debian-based Linux system, enter the
    following command:

    ```sh
    sudo apt-get update
    sudo apt-get install sbcl git
    ```

 2. Install Quicklisp with the following commands:

    ```sh
    curl -O https://beta.quicklisp.org/quicklisp.lisp
    sbcl --load quicklisp.lisp --eval "(quicklisp-quickstart:install)" --quit
    sbcl --load ~/quicklisp/setup.lisp --eval "(ql:add-to-init-file)" --quit
    ```

 3. From here on, we assume that all commands are being run in the
    top-level directory of this project.  Set up dependencies
    necessary to run this project by running this command within the
    top-level directory of this project:

    ```sh
    make live
    ```

    This creates a `_live` directory within the current directory and
    copies all necessary dependencies to it.

 4. Create data and log directories:

    ```sh
    sudo mkdir -p /opt/data/mathb/ /opt/log/mathb/
    sudo cp -R meta/data/* /opt/data/mathb/
    sudo chown -R "$USER" /opt/data/mathb/ /opt/log/mathb/
    ```

    By default, MathB reads post data from and writes posts to
    `/opt/data/mathb/`.  It writes logs to `/opt/log/mathb/` by
    default.  The next section explains how to make it use custom
    directory paths.

 4. Run MathB with the following command:

    ```sh
    sbcl --load mathb.lisp
    ```

 5. Visit http://localhost:4242/ with a web browser to use MathB.

After starting MathB in this manner, click on the various navigation
links and make a new post to confirm that MathB is working as
expected.


Custom Directory Paths
----------------------

In the previous section, we created a data directory at
`/opt/data/mathb/` and a log directory at `/opt/log/mathb/`.  By
default, MathB writes new posts to and reads posts from this directory
path.  To make it use a different path for the data directory, set the
variable named `*data-directory*` before loading it.  Similarly, set
the variable named `*log-directory*` to specify a different path for
the log directory.  The following steps demonstrate how to do this:

 1. Create data directory at a custom path, say, at `~/data`:

    ```sh
    mkdir -p ~/data/ ~/log/
    cp -R meta/data/* ~/data/
    ```

 2. Run MathB with the following command:

    ```sh
    sbcl --eval '(defvar *data-directory* "~/data/")' \
         --eval '(defvar *log-directory* "~/log/")' \
         --load mathb.lisp
    ```

 3. Visit http://localhost:4242/ with a web browser to use MathB.

After starting MathB in this manner, click on the various navigation
links and make a new post to confirm that MathB is working as
expected.


Data Files
----------

The data directory contains the following files:

 - [`opt.lisp`]: This file contains a property list that can be
   modified to alter the behaviour of MathB.  This is explained in
   detail in the next section.

 - [`slug.txt`]: This file contains the ID of the latest post
   successfully saved.

 - [`post/X/Y/*.txt`]: These files contain the actual posts submitted
   by users where `X` and `Y` are placeholders for two integers
   explained shortly.  Each `.txt` file contains a post submitted by a
   user.

In the last point, the placeholder `X` is the post ID divided
by 1000000.  The placeholder `Y` is the post ID divided by 1000.  For
example, for a post with ID 1, `X` is `0` and `Y` is `0`, so a post
with this ID is saved at `post/0/0/1.txt`.  For a more illustrative
example, consider a post with with ID 2301477.  Now `X` is `2` and `Y`
is `2301`, so a post with this ID is saved at
`post/2/2301/2301477.txt`.

Let us call each `X` directory a short-prefix directory and each `Y`
directory under it a long-prefix directory.  As a result of the
calculation explained above, each short-prefix directory contains a
maximum of 1000 long-prefix directories and each long-prefix directory
contains a maximum of 1000 post files.  Thus, each short-prefix
directory contains a maximum of one million post files under it.

[`opt.lisp`]: meta/data/opt.lisp
[`slug.txt`]: meta/data/slug.txt
[`post/X/Y/*.txt`]: meta/data/post/0/0


Runtime Options
---------------

MathB reads runtime properties from `opt.lisp`.  This file contains a
property list.  Each property in this list is followed by a value for
that property.  This property list may be used to alter the behaviour
of MathB.  A list of all supported properties and their descriptions
is provided below.

  - `:lock-down` (default is `nil`): A value of `t` makes MathB run in
    lock-down mode, i.e., existing posts cannot be viewed and new
    posts cannot be submitted.

  - `:read-only` (default is `nil`): A value of `t` makes MathB run in
    read-only mode, i.e., old posts can be viewed but new posts cannot
    be made.  If the values of both this property and the previous
    property are `nil`, then MathB runs normally in read-write mode.

  - `:min-title-length` (default is `0`): The minimum number of
    characters allowed in the title field.

  - `:max-title-length` (default is `120`): The maximum number of
    characters allowed in the title field.

  - `:min-name-length` (default is `0`): The minimum number of
    characters allowed in the name field.

  - `:max-name-length` (default is `120`): The maximum number of
    characters allowed in the name field.

  - `:min-code-length` (default is `1`): The minimum number of
    characters allowed in the code field.

  - `:max-code-length` (default is `10000`): The maximum number of
    characters allowed in the code field.

  - `:global-post-interval` (default is `0`): The minimum interval (in
    seconds) required between two consecutive successful posts.

    Example: If this value is `10` and one client submits a new post
    at 10:00:00 and another client submits a post at 10:00:07, the
    post of the second client is rejected with an error message that
    they must wait for 3 more seconds before submitting the post.  An
    attempt to submit the post at 10:00:10 or later would succeed,
    provided that no other client submitted another post between
    10:00:10 and the second client's attempt to make a post.

  - `:client-post-interval` (default is `0`): The minimum interval (in
    seconds) between two consecutive successful posts allowed from the
    same client.

    Example: If this value is `10` and one client submits a new post
    at 10:00:00, then the same client is allowed to make the next
    successful post submission at 10:00:10 or later.  If the same
    client submits another post at 10:00:07, the post is rejected with
    an error message that they must wait for 3 more seconds before
    submitting the post.  This does not affect the posting behaviour
    for other clients.  For example, another client can successfully
    submit their post at 10:00:07 while the first client cannot.

  - `:expect` (default is `nil`): A list of strings.  At least one
    string from this list must occur in the submitted code field.

    Example: If this value is `("\(" "\[")` and the submitted post
    contains `\[ 1 + 1 = 2. \]` in the code field, then the post is
    accepted successfully.  However, if the submitted code contains
    only `1 + 1 = 2`, then the post is rejected because neither the
    string `"\("` nor the string `"\["` occurs in the code field of
    this submission.

  - `:block` (default is `nil`): A list of strings that are not
    allowed in a post.  If a post contains any string in this list,
    the post is rejected and the input form is returned intact to the
    client.

    Example: If this value is `("berk" "naff" "xxx")` and a client
    posts content which contains the string `xxx` in any field (code,
    title, or name), the post is rejected.

  - `:ban` (default is `nil`): A list of IPv4 or IPv6 address
    prefixes.  If the address of the remote client (as it appears in
    the logs) matches any prefix in this list, the post from the
    client is rejected.  The prefixes must be expressed as simple
    string literals.  CIDRs, globs, regular expressions, etc. are not
    supported.  A dollar sign (`$`) at the end of a prefix string
    matches the end of the client's address string.

    Example: Let us consider a value of `("10.1." "10.2.0.2"
    "10.3.0.2$")` for this property.  If a client from IP address
    `10.1.2.3` submits a post, it is rejected because the prefix
    `10.1.` matches this IP address.  If a client from IP address
    `10.2.0.23` submits a post, it is rejected because the prefix
    `10.2.0.2` matches this IP address.  If a client from IP address
    `10.3.0.2` submits a post, it is rejected because the prefix
    `10.3.0.2$` matches this IP address.  If a client from IP address
    `10.3.0.23` submits a post, it is accepted because none of the
    prefixes match this IP address.

  - `:protect` (default is `0`): The maximum ID of protected posts.
    If MathB determines that the post ID of the next post is less than
    or equal to this value, then it rejects the post.  Setting this
    property is almost never required.  However, it is provided for
    paranoid administrators who might worry what would happen if the
    data file `slug.txt` ever becomes corrupt.  This property ensures
    that in case this data file ever becomes corrupt, MathB would
    never ever overwrite old posts with IDs less than or equal to the
    number set for this property.

    Example: Let us assume that the current value in `slug.txt`
    is 1200.  Now normally, the next time a client submits a new post,
    their post would be saved with an ID of 1201 and the value in
    `slug.txt` would be incremented to 1201.  But instead, let us
    assume that due to an unforeseen scenario (say, a bug in MathB or
    a hardware failure), the value in `slug.txt` is corrupted to `12`.
    With a value of `0` for `:protect`, MathB would overwrite an
    existing post at `post/0/0/13.txt`.  However, with a value of say,
    `100` for `:protect`, MathB would refuse to overwrite the existing
    port.

If a property name is missing from this file or if the file itself is
missing, then the default value of the property mentioned within
parentheses above is used.

Whenever a post is rejected due to a runtime option, the entire input
form is returned intact to the client with an error message, so that
they can fix the errors or wait for the suggested post interval and
resubmit the post again.

The property values in `opt.lisp` may be modified at any time, even
while MathB is running.  It is not necessary to restart MathB after
changing property values in `opt.lisp`.  The changes are picked up
automatically while processing the next HTTP POST request.


Template Files
--------------

There are two template files to generate the HTML pages sent to the
clients:

  - [`web/html/mathb.html`]: This template file is used to generate
    the HTML response for the home page, a mathematical snippet page,
    as well as an HTTP response page when the post is rejected due to
    a validation error.

  - [`web/html/error.html`]: This template file is used to generate
    HTTP error pages.

A template file may be modified at any time, even while MathB is
running.  It is not necessary to restart MathB after changing a
template file.  The changes are picked up automatically while
processing the next HTTP request.

[`web/html/mathb.html`]: web/html/mathb.html
[`web/html/error.html`]: web/html/error.html


Static Files
------------

There are three types of static files that MathB uses to for its HTML
pages:

  - [`web/js/`]: This directory contains the JavaScript files that
    perform input rendering as a user types out content in the input
    form.

  - [`web/css/`]: This directory contains the stylesheets for the HTML
    pages generated by MathB.

  - [`web/img/`]: This directory contains the favicons for the
    website.  These icons are generated using a LaTeX project in the
    [`meta/logo/`] directory.

A static file may be modified at any time, even while MathB is
running.  It is not necessary to restart MathB after adding, deleting,
or editing a static file.  However, it is necessary to run `make live`
(in the top-level directory of the project) to copy the static files
to the live directory (explained in the next section) from which MathB
serves the static files.

[`web/js/`]: web/js/
[`web/css/`]: web/css/
[`web/img/`]: web/img/
[`meta/logo/`]: meta/logo/


Live Directory
--------------

MathB needs to pull additional JavaScript libraries named TeXMe,
Marked, and MathJax that are essential for rendering Markdown and
LaTeX input.  This is done by running the following command in the
top-level directory of this project:

```sh
make live
```

The above command creates a `_live` directory from scratch, copies the
static files to it, then pulls the additional JavaScript libraries
into it, and sets up the `_live` directory, so that MathB can serve
the static files from it.

The live directory should never be modified directly because every
`make live` run deletes the entire directory and creates it from
scratch again.  Any modification necessary should be made to the
template files or static files explained in the previous two sections.


Print
-----

While the primary purpose of this project is to allow users to write
mathematical snippets, save them, and share a link to them with
others, the stylesheet used in this project takes special care to
allow printing beautifully rendered pages to paper.

When a MathB page is printed, only the rendered content appears in the
print.  The input form, buttons, navigation links, and other user
interface elements do not appear in the print.


Save PDF
--------

It is possible to turn a MathB post into a PDF file using the printing
facility of most web browsers running on a desktop or laptop.  The
exact steps to save a web page as PDF vary from browser to browser but
the steps to do so look roughly like this:

- Select File > Print from the web browser menu.
- Then in the print window or dialog box that comes up,
  deselect/disable the options to print headers and footers.
- Finally, choose the option to save a PDF.

If everything works as expected, the saved PDF should contain only the
rendered content with all mathematical formulas rendered properly.
The web pages generated by this project use special styling rules to
ensure that the input form, buttons, navigation links, and other user
interface elements do not appear in saved PDF.


History
-------

MathB.in was the longest-running mathematics pastebin, serving its
community of users for over a decade until its discontinuation in
March 2025.  It was not the first mathematics pastebin on the world
wide web though.  It was the second one.  The first one was created by
Mark A. Stratman which was hosted at `mathbin.net` until 2020.

MathB.in was launched on Sunday, 25 March 2012, after a single night
of furious coding.  This was a result of stumbling upon
[math.stackexchange.com][] the previous night, which used MathJax to
render mathematical formulas in a web browser.  Thanks to that chance
encounter with MathJax, the rest of that Saturday night was spent
coding a new mathematics pastebin using MathJax and PHP.  After coding
through the night, registering a domain name, and setting up a
website, MathB.in was released early Sunday morning.

Over the years, MathB.in received occasional refinements.  It was
eventually rewritten in Common Lisp, replacing its original PHP
implementation.  For a detailed account of its evolution, see the post
[MathB.in Turns Ten][].

MathB.in remained a useful tool for IRC communities, students,
educators, and collaborators for 13 years.  It was eventually shut
down on Sunday, 16 March 2025.  At the time of its discontinuation, it
held the distinction of being the longest-running mathematics
pastebin.  For more details about why this service was discontinued,
see the post [MathB.in Is Shutting Down][].

Although the original MathB.in service no longer exists, its source
code is still available as free and open-source software through this
project.

[math.stackexchange.com]: https://math.stackexchange.com/
[MathB.in Turns Ten]: https://susam.net/blog/mathbin-turns-ten.html
[MathB.in Is Shutting Down]: https://susam.net/blog/mathbin-is-shutting-down.html


License
-------

This is free and open source software.  You can use, copy, modify,
merge, publish, distribute, sublicense, and/or sell copies of it,
under the terms of the MIT License.  See [LICENSE.md][L] for details.

This software is provided "AS IS", WITHOUT WARRANTY OF ANY KIND,
express or implied.  See [LICENSE.md][L] for details.

[L]: LICENSE.md


Support
-------

To report bugs, suggest improvements, or ask questions, [create
issues][issues].

[issues]: https://github.com/susam/mathb/issues


Channels
--------

The author of this project hangs out at the following places online:

- Website: [susam.net](https://susam.net)
- GitHub: [@susam](https://github.com/susam) on GitHub
- Mastodon: [@susam@mastodon.social](https://mastodon.social/@susam)

You are welcome to subscribe to, follow, or join one or more of the
above channels to receive updates from the author or ask questions
about this project.


More
----

If you like this project, check out related projects
[TeXMe](https://github.com/susam/texme) and
[Muboard](https://github.com/susam/muboard).
