window.onload = function() {
  var facebook_login_link = document.getElementById("facebook_login_link");
  var facebook_login_url = document.getElementById("facebook_login_url");
  var app_id_input = document.getElementsByName("Settings[app_id]")[0];
  var app_secret_input = document.getElementsByName("Settings[app_secret]")[0];
  var event_page_name_input = document.getElementsByName("Settings[event_page_name]")[0];

  function getLoginLink( callback ) {
    var httpRequest = new XMLHttpRequest();
    httpRequest.onreadystatechange = () => {
      if (httpRequest.readyState === XMLHttpRequest.DONE) {
        if (httpRequest.status === 200) {
          callback(httpRequest.responseText);
        } else {
          alert('There was a problem with the request.');
        }
      }
    }
    httpRequest.open('GET', '/get_login_url');
    httpRequest.send();
  }

  function checkInput() {
    if( app_id_input.value !== '' && app_secret_input.value !== '' && event_page_name_input.value !== '') {
      $('form').request('onSave', {
        data: {redirect:0},
        success: function() {
            console.log('Finished!');
        }
      });
    } else {
      console.log('some inputs missing');
    }
  }

  $('form').on('ajaxSuccess', function() {
    $('form').request('onSave', {
      data: {redirect:0},
      success: function() {
          getLoginLink( function(loginLink)
          {
            console.log('returned: ',loginLink)
            window.location.href = loginLink;
          });
      }
    });
    console.log('Updated!');
  })

  app_id_input.addEventListener('input', checkInput);
  app_secret_input.addEventListener('input', checkInput);
  event_page_name_input.addEventListener('input', checkInput);
};
