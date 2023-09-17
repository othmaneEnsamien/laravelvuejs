import Dashboard from './components/Dashboard.vue';
import ListAppointments from './pages/appointments/ListAppointments.vue';
import Profile from './pages/profiles/Profile.vue';
import Settings from './pages/settings/Settings.vue';
import UserList from './pages/Users/UserList.vue';
import AppointmentForm from './pages/appointments/AppointmentForm.vue';

export default
    [
        {
            path: '/admin/dashboard',
            name: 'admin.dashboard',
            component: Dashboard,
        },
        {
            path: '/admin/appointments',
            name: 'admin.appointments',
            component: ListAppointments,
        },
        {
            path: '/admin/appointments/create',
            name: 'admin.appointments.create',
            component: AppointmentForm,
        },
        {
            path: '/admin/appointments/:id/edit',
            name: 'admin.appointments.edit',
            component: AppointmentForm,
        },

        {
            path: '/admin/profile',
            name: 'admin.profile',
            component: Profile,
        },
        {
            path: '/admin/settings',
            name: 'admin.settings',
            component: Settings,
        },
        {
            path: '/admin/users',
            name: 'admin.users',
            component: UserList,
        },

    ]