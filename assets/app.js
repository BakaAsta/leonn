import { registerReactControllerComponents } from '@symfony/ux-react';
import './bootstrap.js';

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './styles/style.css';
// import './js/alpine-collaspe.min.js';
// import './js/alpine-persist.min.js';
// import './js/alpine.min.js';
// import './js/custom.js';
// import './js/alpine-ui.min.js';
// import './js/alpine-focus.min.js';

registerReactControllerComponents(require.context('./react/controllers', true, /\.(j|t)sx?$/));