global.OC = {
  requestToken: 'test-token',
  generateUrl: (url) => url,
  linkTo: (app, file) => file,
  currentUser: 'test-user'
};

global.t = (app, text) => text;
global.n = (app, text, textPlural, count) => count === 1 ? text : textPlural;

// Mock firstDay which seems to be a global in timer.js
global.firstDay = 1;
