// On fetch les notification s'il y en a
fetch('?page=notification&action=fetch')
  .then(res => res.json())
  .then(data => {
      if(data.length > 0) {
          document.querySelector('.notif-btn').classList.add('has-notif');
      }
  });
