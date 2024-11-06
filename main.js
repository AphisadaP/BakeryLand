function toggle_visibility(id) {
  var e = document.getElementById(id);
  if (e.style.display === 'block') {
      e.style.display = 'none';
  } else {
      e.style.display = 'block';
  }
}

window.onresize = function(event) {
  var e = document.getElementById("menu");
  var s = document.getElementById("search");
  var w = window.innerWidth;

  if (w > 599) {
      if (e) {
          e.style.display = 'block';
      }
      if (s) {
          s.style.display = 'block';
      }
  } else {
      if (e) {
          e.style.display = 'none';
      }
      if (s) {
          s.style.display = 'none';
      }
  }
};
