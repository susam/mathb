window.onload = function () {
  const input = document.getElementById('input')
  const output = document.getElementById('output')
  const url = document.getElementById('url')
  const copy = document.getElementById('copy')

  let timeout = null

  function renderOutput() {
    let code = input.value
    code = code.trim()
    output.innerHTML = texme.render(code)
    window.MathJax.typeset()
  }

  function handleInput() {
    renderOutput()
  }

  // Schedule input handler to process input after a short delay.
  //
  // When the user edits an element in the input form, the
  // corresponding element of the output sheet is not updated
  // immediately for two reasons:
  //
  //   - A fast typist can type 7 to 10 characters per second. Updating
  //     the output sheet so frequently, causes the user interface to
  //     become less responsive.
  //
  //   - The onpaste or oncut functions of an input element gets
  //     the old value of the element instead of the new value
  //     resulting from the cut or paste operation.
  //
  // This function works around the above issues by scheduling the
  // handleInput() function to be called after 100 milliseconds. This
  // ensures that the output is not updated more than 10 times per
  // second. This also ensures that when the handleInput() function is
  // invoked as a result of a cut or paste operation on a text field
  // element, then it gets the updated value of the element.
  function scheduleInputHandler() {
    if (timeout !== null) {
      window.clearTimeout(timeout)
      timeout = null
    }
    timeout = window.setTimeout(handleInput, 100)
  }

  function copyURL() {
    url.select()
    document.execCommand('copy')
    window.setTimeout(function () { url.blur() }, 125)
  }

  function init() {
    input.onkeyup = scheduleInputHandler
    input.onpaste = scheduleInputHandler
    input.oncut = scheduleInputHandler
    copy.onclick = copyURL
  }

  init()
}
