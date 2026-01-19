const modal = document.getElementById('profileModal')
const openBtn = document.getElementById('editProfil')
const closeBtn = document.getElementById('closeModal')

openBtn.addEventListener('click', () => {
  modal.style.display = 'flex'
})

closeBtn.addEventListener('click', () => {
  modal.style.display = 'none'
})

modal.addEventListener('click', e => {
  if (e.target === modal) {
    modal.style.display = 'none'
  }
})

const eye = document.getElementById('eyeMdp')
const eye1 = document.getElementById('eyeVerif')
const mdp = document.getElementById('password')
const mdpVerif = document.getElementById('passwordVerif')

//listener
eye.addEventListener('click', () => {
  mdp.type = mdp.type === 'password' ? 'text' : 'password'
  eye.classList.toggle('fa-eye')
  eye.classList.toggle('fa-eye-slash')
})

if (eye1 !== null) {
  eye1.addEventListener('click', () => {
    mdpVerif.type = mdpVerif.type === 'password' ? 'text' : 'password'
    eye1.classList.toggle('fa-eye')
    eye1.classList.toggle('fa-eye-slash')
  })
}

const deleteModal = document.getElementById('deleteModal')
const deleteText = document.getElementById('deleteText')

document.querySelectorAll('.deleteFriend').forEach(btn => {
  btn.addEventListener('click', e => {
    e.preventDefault()

    const form = document.querySelector('#deleteModal form')
    let input = document.createElement('input')
    input.type = 'hidden'
    input.name = 'idToDelete'
    input.value = btn.dataset.id
    form.appendChild(input)

    deleteText.innerHTML = `Voulez-vous vraiment supprimer : <strong>${btn.dataset.title}</strong> ?`

    deleteModal.style.display = 'flex'
  })
})

let quizIdToDelete = null
let quizGenreToDelete = null

document.querySelectorAll('.deleteQuiz').forEach(btn => {
  btn.addEventListener('click', e => {
    e.preventDefault()

    const form = document.querySelector('#deleteModal form')
    let input = document.createElement('input')
    input.type = 'hidden'
    input.name = 'idToDelete'
    input.value = btn.dataset.id
    form.appendChild(input)

    let inputBis = document.createElement('input')
    inputBis.type = 'hidden'
    inputBis.name = 'genre'
    inputBis.value = btn.dataset.genre
    form.appendChild(inputBis)

    deleteText.innerHTML = `Voulez-vous vraiment supprimer : <strong>${btn.dataset.title}</strong> ?`

    deleteModal.style.display = 'flex'
  })
})

// fermer
document.getElementById('closeDeleteModal').onclick = document.getElementById(
  'cancelDelete'
).onclick = () => {
  deleteModal.style.display = 'none'
}

// clic overlay
deleteModal.addEventListener('click', e => {
  if (e.target === deleteModal) {
    deleteModal.style.display = 'none'
  }
})

const uploadBtn = document.getElementById('uploadBtn');
const avatarInput = document.getElementById('avatarInput');
const form = document.getElementById('avatarForm');

uploadBtn.addEventListener('click', () => {
  avatarInput.click();
});

// Quand un fichier est choisi → envoyer le formulaire
avatarInput.addEventListener('change', () => {
  if (avatarInput.files.length > 0) {
    form.submit();
  }
});

