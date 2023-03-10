import './bootstrap';

// INERTIA
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

// QUASAR
import { Quasar, Notify, Dialog } from 'quasar'
import quasarLang from 'quasar/lang/pt-BR'
import '@quasar/extras/material-icons/material-icons.css'
import '@quasar/extras/material-icons-outlined/material-icons-outlined.css'
import '@quasar/extras/material-icons-round/material-icons-round.css'
import 'quasar/src/css/index.sass'

// Calendar Style
import '@quasar/quasar-ui-qcalendar/src/QCalendarMonth.sass'
import '@quasar/quasar-ui-qcalendar/src/QCalendarVariables.sass'
import '@quasar/quasar-ui-qcalendar/src/QCalendarTransitions.sass'

// SASS
import '../sass/app.sass';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {
         createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(Quasar, {
                plugins: {
                    Notify, Dialog
                },
                lang: quasarLang,
                config: {
                    brand: {
                      primary: '#4F8BE6',
                      negative: '#FF3945'
                    }
                }
            })
            .mount(el);
    },
});

InertiaProgress.init({ color: 'blue' });
