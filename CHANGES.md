Changelog
=========

1.2.0 (UNRELEASED)
------------------

### Added

- Runtime property `:lock-down` to disable post viewing and post submission.
- Runtime property `:min-title-length` to specify minimum title length.
- Runtime property `:min-name-length` to specify minimum name length.
- Runtime property `:min-code-length` to specify minimum code length.
- Runtime property `:initial-year` to customize the initial yearin footer.
- Runtime property `:copyright-owner` to customize the owner name in footer.
- Link to privacy notice.
- Show only rendered content in print preview mode.


### Changed

- Do not allow code textarea to be dragged to overlap with output sheet.


### Fixed

- Rendering error when the input contains commands that require
  additional MathJax extensions.


1.1.0 (2022-09-30)
------------------

### Added

- Runtime property `:ban` to reject posts from specific IP addresses.
- Runtime property `:protect` to protect posts in case of data corruption.
- Nginx configuration to work around Hunchentoot memory leakage issue.
- Show metadata at URL path `/0`.


### Changed

- Empty header value in post text file no longer has a trailing space.


### Fixed

- Table rows disappearing from rendered output.
- Incorrect zero top margin for display math.
- HTTP 500 error on post submission when options file is missing.


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
- Runtime property `:read-only` to run MathB in read-only mode.
- Runtime property `:max-title-length` to specify maximum title length.
- Runtime property `:max-name-length` to specify maximum name length.
- Runtime property `:max-code-length` to specify maximum code length.
- Runtime property `:global-post-interval` to specify minimum interval
  between two posts from arbitrary clients.
- Runtime property `:client-post-interval` to specify minimum interval
  between two posts from the same client.
- Runtime property `:block` to specify blocked strings in post.


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
