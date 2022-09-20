MathB
=====

MathB is a mathematics pastebin software that powers
[MathB.in](https://mathb.in/). It is a web-based service meant for
sharing snippets of mathematical text with others on the world wide
web. This software and the website was born out of a one night hack on
Sunday, March 25, 2012. It was initially written in PHP and then later
rewritten in Common Lisp.

Visit https://mathb.in/ to use the mathematics pastebin.


Contents
--------

* [Quick Start](#quick-start)
* [Data Directory](#data-directory)
* [Data Files](#data-files)
* [Runtime Options](#runtime-options)
* [Templates Files](#template-files)
* [Static Files](#static-files)
* [Live Directory](#live-directory)
* [License](#license)
* [Support](#support)
* [Channels](#channels)
* [More](#more)


Quick Start
-----------

This section explains how to run MathB locally. The steps assume a
macOS, Debian, or Debian-based Linux distribution.

 1. Install SBCL and Git.
 
    On macOS, enter the following command if you have Homebrew:
    
    ```sh
    brew install sbcl git
    ```
    
    On Debian, Ubuntu, or another Debian-based Linux system, enter the
    following command:
    
    ```sh
    sudo apt-get update && sudo apt-get install sbcl git
    ```
    
 2. Install Quicklisp with the following commands:
 
    ```sh
    curl -O https://beta.quicklisp.org/quicklisp.lisp
    sbcl --load quicklisp.lisp --eval "(quicklisp-quickstart:install)" --quit
    sbcl --load ~/quicklisp/setup.lisp --eval "(ql:add-to-init-file)" --quit
    ```
    
 3. From here on, we assume that all commands are being run in the
    top-level directory of this project. Set up dependencies necessary
    to run MathB by running this command within the top-level
    directory of this project:
 
    ```sh
    make live
    ```
    
    This creates a `_live` directory within the current directory and
    copies all necessary dependencies to it.
    
 4. Create MathB data directory:
 
    ```sh
    sudo mkdir -p /opt/data/mathb/
    sudo cp -R meta/data/* /opt/data/mathb/
    sudo chown -R "$USER" /opt/data/mathb/
    ```
    
    By default, MathB reads post data from and writes post to
    `/opt/data/mathb/`. The next section explains how to make MathB
    use a custom data directory path.
 
 4. Run MathB with the following command:
 
    ```sh
    sbcl --load mathb.lisp
    ```
    
 5. Visit http://localhost:4242/ with a web browser to use MathB.
 
After starting MathB in this manner, click on the various navigation
links and make a new post to confirm that MathB is working as
expected.


Data Directory
--------------

In the previous section, we created a data directory at
`/opt/data/mathb/`. By default, MathB writes new posts to and reads
posts from this directory path. To make MathB use a different path for
the data directory, set the variable named `*data-directory*` before
loading MathB. The following steps explain how to do this:

 1. Create MathB data directory at a custom path, say, at `~/data`:
 
    ```sh
    mkdir -p ~/data/
    cp -R meta/data/* ~/data/
    ```
    
 2. Run MathB with the following command:
 
    ```sh
    sbcl --eval '(defvar *data-directory* "~/data/")' --load mathb.lisp
    ```
    
 3. Visit http://localhost:4242/ with a web browser to use MathB.
 
After starting MathB in this manner, click on the various navigation
links and make a new post to confirm that MathB is working as
expected.


Data Files
----------

The data directory contains the following files:

 - `opt.lisp`: This file contains a property list that can be modified
   to alter the behaviour of MathB. This is explained in detail in the
   next section.
   
 - `slug.txt`: This file contains the ID of the latest post
   successfully saved.
   
 - `post/X/Y/*.txt`: These files contain the actual posts submitted by
   users where `X` and `Y` are placeholders for two integers explained
   shortly. Each `.txt` file contains a post submitted by a user.
   
In the last point, the placeholder `X` is the post ID divided
by 1000000. The placeholder `Y` is the post ID divided by 1000. For
example, for a post with ID 1, `X` is `0` and `Y` is `0`, so a post
with this ID is saved at `post/0/0/1.txt`. For a more illustrative
example, consider a post with with ID 2301477. Now `X` is `2` and `Y`
is `2301`, so a post with this ID is saved at
`post/2/2301/2301477.txt`.

Let us call each `X` directory a short-prefix directory and each `Y`
directory under it a long-prefix directory. As a result of the
calculation explained above, each prefix directory contains a maximum
of 1000 long-prefix directories and each long-prefix directory
contains a maximum of 1000 post files. Thus, each short-prefix
directory contains a maximum of one million post files under it.


Runtime Options
---------------

MathB reads runtime properties from `opt.lisp`. This file contains a
property list. Each property in this list is followed by a value for
that property. This property list may be used to alter the behaviour
of MathB. A list of all supported properties and their descriptions is
provided below.

  - `:read-only` (default is `nil`): A value of `t` makes MathB run in
    read-only mode, i.e., old posts can be viewed but new posts cannot
    be made. A value of `nil` makes MathB run normally in read-write
    mode.
    
    Example: If this value is `t`, new posts are rejected.
    
  - `:max-title-length` (default is `120`): The maximum number of
    characters allowed in the title field.
    
  - `:max-name-length` (default is `120`): The maximum number of
    characters allowed in the name field.
    
  - `:max-code-length` (default is `10000`): The maximum number of
    characters allowed in the code field.
  
  - `:global-post-interval` (default is `0`): The minimum interval (in
    seconds) between two consecutive successful posts allowed on
    MathB.
    
    Example: If this value is `10` and one client submits a new post
    at 10:00:00 and another client submits a post at 10:00:07, the
    post of the second client is rejected with an error message that
    they must wait for 3 more seconds before submitting the post. An
    attempt to submit the post at 10:00:10 or later would succeed,
    provided that no other client submitted another post between
    10:00:10 and the second client's attempt to make a post.
    
  - `:client-post-interval` (default is `0`): The minimum interval (in
    seconds) between two consecutive successful posts allowed from the
    same client.
    
    Example: If this value is `10` and one client submits a new post
    at 10:00:00, then the same client is be allowed to make the next
    successful post submission at 10:00:10 or later. If the same
    client submits another post at 10:00:07, the post is rejected with
    an error message that they must wait for 3 more seconds before
    submitting the post. This does not affect the posting behaviour
    for other clients. For example, another client can successfully
    submit their post at 10:00:07 while the first client could not.
    
  - `:block` (default is `nil`): A list of strings that are not
    allowed in a post. If a post contains any string in this list, the
    post is rejected and the input form is returned intact to the
    client.
    
    Example: If this value is `("berk" "naff" "xxx")` and a client
    posts content which contains the string `xxx` in any field (code,
    title, or name), the post is rejected.
    
If a property name is missing from this file or if the file itself is
missing, then MathB defaults to the default value of the property
mentioned within parentheses above.

Whenever MathB rejects a post due to a runtime option, the entire
input form is returned intact to the client with an error message, so
that they can fix the errors or wait for the suggested post interval
and resubmit the post again.

The property values in `opt.lisp` may be modified at any time, even
while MathB is running. It is not necessary to restart MathB after
changing property values in `opt.lisp`. MathB automatically picks up
the changes while processing the next HTTP POST request.


Template Files
--------------

There are two template files to generate the HTML pages sent to the
clients:

  - [web/mathb.html](web/mathb.html): This template file is used to
    generate the HTML response for the home page, a mathematical
    snippet page, as well as an HTTP response page when the post is
    rejected due to a validation error.
  
  - [web/error.html](web/error.html): This template file is used to
    generate HTTP error pages.

A template file may be modified at any time, even while MathB is
running. It is not necessary to restart MathB after changing a
template file. MathB automtaically picks up the template changes while
processing the next HTTP request.


Static Files
------------

There are three types of static files that MathB uses to for its HTML
pages:

  - [web/js/](web/js): This directory contains the JavaScript files
    that perform input rendering as a user types out content in the
    input form.
    
  - [web/css/](web/css): This directory contains the stylesheets for
    the HTML pages generated by MathB.

  - [web/favicon.*](web/): These are favicons for MathB pages. These
    icons are generated using a LaTeX project in the
    [meta/logo/](meta/logo/) directory.

A static file may be modified at any time, even while MathB is
running. It is not necessary to restart MathB after adding, deleting,
or editing a static file. However, it is necessary to run `make live`
(in the top-level directory of the project) to copy the static files
to the live directory (explained in the next section) from which MathB
serves the static files.


Live Directory
--------------

MathB needs to pull additional JavaScript libraries named TeXMe,
Marked, and MathJax that are essential for rendering Markdown and
LaTeX input. This is done by running the following command in the
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
scratch again. Any modification necessary should be made to the
template files or static files explained in the previous two sections.


License
-------

This is free and open source software. You can use, copy, modify,
merge, publish, distribute, sublicense, and/or sell copies of it,
under the terms of the MIT License. See [LICENSE.md][L] for details.

This software is provided "AS IS", WITHOUT WARRANTY OF ANY KIND,
express or implied. See [LICENSE.md][L] for details.

[L]: LICENSE.md


Support
-------

To report bugs, suggest improvements, or ask questions,
[create issues][issues].

[issues]: https://github.com/susam/mathb/issues


Channels
--------

The author of this project hangs out at the following places online:

- [susam.net](https://susam.net) on the Web
- [@susam](https://twitter.com/susam) on Twitter
- [@susam](https://github.com/susam) on GitHub
- [#susam](https://web.libera.chat/#susam) on Libera
- [#susam](https://app.element.io/#/room/#susam:matrix.org) on Matrix

You are welcome to subscribe to, follow, or join one or more of the
above channels to receive updates from the author or ask questions
about this project.


More
----

If you like this project, check out related projects
[TeXMe](https://github.com/susam/texme) and
[Muboard](https://github.com/susam/muboard).
