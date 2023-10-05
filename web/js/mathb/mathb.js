window.onload = function () {
  const form = document.getElementById('form')
  const code = document.getElementById('code')
  const title = document.getElementById('title')
  const name = document.getElementById('name')
  const submit = document.getElementById('submit')
  const view = document.getElementById('view')
  const error = document.getElementById('error')
  const widget = document.getElementById('widget')
  const url = document.getElementById('url')
  const copy = document.getElementById('copy')
  const slug = parseInt(window.location.pathname.substring(1))

  let rendering = false
  let timeout = null

  const allowedNodes = [
    "#text", "blockquote", "br", "code", "div", "em",
    "h1", "h2", "h3", "h4", "h5", "h6",
    "hr", "li", "ol", "p", "pre", "strong",
    "table", "tbody", "td", "tfoot", "th", "thead", "tr", "ul"
  ]

  function removeMarkup(s) {
    const div = document.createElement('div')
    div.innerHTML = s
    return div.innerText
  }

  function sanitizeDOM(node) {
    if (allowedNodes.indexOf(node.nodeName.toLowerCase()) === -1) {
      node.parentNode.removeChild(node)
      return
    }
    if (node.nodeType === Node.ELEMENT_NODE) {
      for (let i = 0; i < node.attributes.length; i++) {
        node.removeAttributeNode(node.attributes[i])
      }
    }
    for (let i = node.childNodes.length - 1; i >= 0; i--) {
      sanitizeDOM(node.childNodes[i])
    }
  }

  function sanitizeHTML(html) {
    if (slug < 10) {
      return html
    }
    const div = document.createElement('div')
    div.innerHTML = html
    sanitizeDOM(div)
    return div.innerHTML
  }

  function renderView() {
    if (rendering) {
      return
    }
    rendering = true

    const titleValue = title.value.trim()
    const nameValue = name.value.trim()
    const codeValue = code.value.trim()

    let h1 = ''
    let h2 = ''
    let body = ''

    if (titleValue !== '') {
      h1 = '<h1 class="title">' + removeMarkup(titleValue) + '</h1>'
    }

    if (nameValue !== '') {
      h2 = '<h2 class="author">' + removeMarkup(nameValue) + '</h2>'
    }

    if (codeValue != '') {
      body = sanitizeHTML(texme.render(codeValue))
    }

    view.innerHTML = h1 + h2 + body
    window.MathJax.texReset()
    window.MathJax.typesetPromise().then(function () {
      rendering = false
    })
  }

  // Schedule input handler to process input after a short delay.
  //
  // When the user edits an element in the input form, the
  // corresponding element of the output sheet is not updated
  // immediately for two reasons:
  //
  //   - A fast typist can type 7 to 10 characters per second.
  //     Updating the output sheet so frequently, causes the user
  //     interface to become less responsive.
  //
  //   - The onpaste or oncut functions of an input element gets the
  //     old value of the element instead of the new value resulting
  //     from the cut or paste operation.
  //
  // This function works around the above issues by scheduling the
  // renderView() function to be called after 100 milliseconds.  This
  // ensures that the output is not updated more than 10 times per
  // second.  This also ensures that when the renderView() function is
  // invoked as a result of a cut or paste operation on a text field
  // element, then it gets the updated value of the element.
  function scheduleInputHandler() {
    if (timeout !== null) {
      window.clearTimeout(timeout)
      timeout = null
    }
    timeout = window.setTimeout(renderView, 100)
  }

  function insertToken() {
    const a = 123 + Math.floor((1000 - 123) * Math.random())
    const b = a % 91
    const c = a % 87
    const x = 1000000 * a + 1000 * b + c
    const input = document.createElement('input')
    input.setAttribute('type', 'hidden')
    input.setAttribute('name', 'token')
    input.setAttribute('value', x.toString())
    form.appendChild(input)
  }

  function copyURL() {
    url.select()
    document.execCommand('copy')
    window.setTimeout(function () { url.blur() }, 125)
  }

  function init() {
    form.onsubmit = insertToken

    code.onkeyup = scheduleInputHandler
    code.onpaste = scheduleInputHandler
    code.oncut = scheduleInputHandler

    title.onkeyup = scheduleInputHandler
    title.onpaste = scheduleInputHandler
    title.oncut = scheduleInputHandler

    name.onkeyup = scheduleInputHandler
    name.onpaste = scheduleInputHandler
    name.oncut = scheduleInputHandler

    url.onclick = url.select
    copy.onclick = copyURL

    code.focus()

    if (window.location.pathname !== '/') {
      url.value = window.location.href
    }

    if (error === null) {
      renderView()
    }
  }

  init()
}
