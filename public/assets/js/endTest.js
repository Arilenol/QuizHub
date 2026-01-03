//gérer les likes et les dislikes de manière dynamique

const like = document.getElementsByClassName('like')[0]
const dislike = document.getElementsByClassName('dislike')[0]

let action = 0 //on regarde s'il y a des changements
let reactions = [] // stocke les réactions à envoyer

// récupérer l'id du quiz depuis l'URL
const params = new URLSearchParams(window.location.search)
const quizId = params.get('id')

like.addEventListener('click', async e => {
  e.preventDefault() // bloque la navigation immédiate

  if (action === 0) {
    like.textContent = increment(like.textContent)
    action = 1
    dislike.disabled = true
    dislike.id = 'disabled'

    // ajouter la réaction
    reactions = [{ type: 'like' }]
  } else {
    like.textContent = desincrement(like.textContent)
    action = 0
    dislike.disabled = false
    dislike.removeAttribute('id')

    // annuler la réaction
    reactions = []
  }

  reactions.push({ quizId: quizId })
  console.log('here')

  // envoyer la réaction au serveur
  const data = { type: 'like', quizId: quizId }
  await fetch('index.php?page=save-reactions', {
    method: 'POST',
    body: JSON.stringify(data),
    headers: { 'Content-Type': 'application/json' }
  })

  // ensuite naviguer vers la page cible
  window.location.href = 'index.php?page=test&id=' + quizId
})

dislike.addEventListener('click', () => {
  if (action === 0) {
    dislike.textContent = increment(dislike.textContent)
    action = 1
    like.disabled = true
    like.id = 'disabled'

    reactions = [{ type: 'dislike' }]
  } else {
    dislike.textContent = desincrement(dislike.textContent)
    action = 0
    like.disabled = false
    like.removeAttribute('id')

    reactions = []
  }
})

function increment (str) {
  return str.replace(/\d+/g, match => Number(match) + 1)
}

function desincrement (str) {
  return str.replace(/\d+/g, match => Number(match) - 1)
}
