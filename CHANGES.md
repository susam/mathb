Changelog
=========

1.1.0 (UNRELEASED)
------------------

### Added

- Runtime property `:ban` to reject posts from specific IP addresses.
- Runtime property `:protect` to protect posts in case of data corruption.
- Nginx configuration to work around Hunchentoot memory leakage issue.
- Show metadata at URL path `/0`.


### Changed

- Empty header value in post text file no longer has a trailing space.
- Table rows disappearing from rendered output.


1.0.0 (2022-09-20)
------------------

### Added

- The first major release of MathB since 2012.
- Common Lisp source code is made available in this release.
- Dark colour scheme for clients that prefer dark colour scheme.
- Responsive layout to adapt the user interface to narrow screens.
- Use TeXMe to render Markdown + LaTeX.
- Add post interval features to control flooding.
- Control runtime behaviour with `opt.lisp`.


### Removed

- No more PHP source code. All application code is in Common Lisp now.


0.2.0 (2020-11-28)
------------------

### Added

- Stop rendering `<img>` elements.


0.1.0 (2013-12-02)
------------------

### Added

- Support Markdown format.
- Reset equation numbers while rendering input.


0.0.1 (2012-03-25)
------------------

### Added

- The first release of MathB written in PHP.
