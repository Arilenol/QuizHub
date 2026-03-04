async function save() {
  const lecon = document.querySelector('.lesson-page')
  const buttons = document.querySelectorAll('.button, .buttonAction')
  const fin = document.querySelectorAll(".fin")
  const reaction = document.querySelectorAll(".quiz-reactions")

  // cacher boutons et reactions
  reaction.forEach(b => (b.style.display = 'none'))
  fin.forEach(b => (b.style.display = 'none'))
  buttons.forEach(b => (b.style.display = 'none'))

  // sauvegarder styles
  const oldStyle = {
    padding: lecon.style.padding,
    width: lecon.style.width,
    background: lecon.style.background,
    boxSizing: lecon.style.boxSizing
  }

  // styles PDF
  lecon.style.padding = "15mm"
  lecon.style.width = "210mm"
  lecon.style.background = "white"
  lecon.style.boxSizing = "border-box"

  let title = document.querySelector('.lesson-header h1').textContent.trim()
  title = title.replace(/\s+/g, '')

  await html2pdf()
    .from(lecon)
    .set({
      margin: 0,
      filename: `leçon_${title}.pdf`,
      html2canvas: {
        scale: 2,
        scrollX: 0,
        scrollY: 0,
      },
      jsPDF: {
        unit: 'mm',
        format: 'a4',
        orientation: 'portrait'
      },
      pagebreak: {
        mode: ['css', 'avoid-all']
      }
    })
    .save()

  // restaurer styles
  lecon.style.padding = oldStyle.padding
  lecon.style.width = oldStyle.width
  lecon.style.background = oldStyle.background
  lecon.style.boxSizing = oldStyle.boxSizing
  buttons.forEach(b => (b.style.display = ''))
  reaction.forEach(b => (b.style.display = ''))
  fin.forEach(b => (b.style.display = ''))
}
