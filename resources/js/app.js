/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');


// window.Vue = require('vue');

// Vue.use(VueResource);


import { createApp } from 'vue';

import CanvasComponent from './components/Canvas.vue';
import GalleryComponent from './components/Gallery.vue';
import GroupImageSelectorComponent from './components/GroupImageSelector.vue';
import BookPreviewComponent from './components/BookPreview.vue';
import WaitingRoomComponent from './components/WaitingRoom.vue';
import WaitingRoomNonAuthComponent from './components/WaitingRoomNonAuth.vue';
import LeaderBoardComponent from './components/LeaderBoard.vue';
import TopRatedComponent from './components/TopRated.vue';
import TopRatedSingleComponent from './components/TopRatedSingle.vue';
import GroupsGridComponent from './components/GroupsGrid.vue';
import SettingsComponent from './components/Settings.vue';
import TrophiesHeader from './components/TrophiesHeader.vue';
import NotificationsHeader from './components/NotificationsHeader.vue';
import UserStatsComponent from './components/UserStats.vue';
import UserChangesComponent from './components/UserChanges.vue';
import UserStatsHeaderComponent from './components/UserStatsHeader.vue';
import RandomMonsterComponent from './components/RandomMonster.vue';
import RandomWordsComponent from './components/RandomWords.vue';
createApp({
        components: {
            CanvasComponent,
            GalleryComponent,
            GroupImageSelectorComponent,
            BookPreviewComponent,
            WaitingRoomComponent,
            WaitingRoomNonAuthComponent,
            LeaderBoardComponent,
            TopRatedComponent,
            TopRatedSingleComponent,
            GroupsGridComponent,
            SettingsComponent,
            TrophiesHeader,
            NotificationsHeader,
            UserStatsComponent,
            UserChangesComponent,
            UserStatsHeaderComponent,
            RandomMonsterComponent,
            RandomWordsComponent
        }
    })
    .mount('#app')

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('canvas-component', require('./components/Canvas.vue').default);
// Vue.component('gallery-component', require('./components/Gallery.vue').default);
// Vue.component('group-image-selector-component', require('./components/GroupImageSelector.vue').default);
// Vue.component('book-preview-component', require('./components/BookPreview.vue').default);
// Vue.component('waiting-room-component', require('./components/WaitingRoom.vue').default);
// Vue.component('waiting-room-non-auth-component', require('./components/WaitingRoomNonAuth.vue').default);
// Vue.component('leader-board-component', require('./components/LeaderBoard.vue').default);
// Vue.component('comment-component', require('./components/Comment.vue').default);
// Vue.component('top-rated-component', require('./components/TopRated.vue').default);
// Vue.component('top-rated-single-component', require('./components/TopRatedSingle.vue').default);
// Vue.component('groups-grid-component', require('./components/GroupsGrid.vue').default);
// Vue.component('settings-component', require('./components/Settings.vue').default);
// Vue.component('trophies-header', require('./components/TrophiesHeader.vue').default);
// Vue.component('notifications-header', require('./components/NotificationsHeader.vue').default);
// Vue.component('user-stats-component', require('./components/UserStats.vue').default);
// Vue.component('user-changes-component', require('./components/UserChanges.vue').default);
// Vue.component('user-stats-header-component', require('./components/UserStatsHeader.vue').default);
// Vue.component('random-monster-component', require('./components/RandomMonster.vue').default);
// Vue.component('random-words-component', require('./components/RandomWords.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// const app = new Vue({
//     el: '#app',
// });