// On fetch les notification s'il y en a
fetch('?page=notification&action=fetch')
  .then(res => res.json())
  .then(data => {
      if(data.length > 0) {
          document.querySelectorAll('.notif-btn')[0].classList.add('has-notif');
          document.querySelectorAll('.notif-btn')[1].classList.add('has-notif');
      }
  });
