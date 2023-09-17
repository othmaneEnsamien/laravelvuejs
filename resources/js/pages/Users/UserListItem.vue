<script setup>
import { formatDate } from '../../helper.js';
import { ref, onMounted } from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';

const toastr = useToastr();

const props = defineProps({
    user: Object,
    index: Number,
    selectAll: Boolean,
});

let selectedRole = ref(null); // Initialisez selectedRole à null

const emit = defineEmits(['userDeleted', 'editUser', 'confirmUserDeletion']);

const roles = ref([
    {
        name: 'ADMIN',
        value: 1
    },
    {
        name: 'USER',
        value: 2,
    }
]);

const changeRole = (user, role) => {
    axios.patch(`/api/users/${user.id}/change-role`, {
        role: role,
    })
    .then(() => {
        toastr.success('Role changed successfully!');
    })
};

const toggleSelection = () => {
    emit('toggleSelection', props.user);
}

// Utilisez onMounted pour définir selectedRole après que user soit défini
onMounted(() => {
    selectedRole.value = props.user.role;
});
</script>
<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" /></td>
        <td>{{ index + 1 }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{user.created_at ? formatDate(user.created_at) : ''}}</td>
    
       
          <td>
            <select class="form-control" v-model="selectedRole" @change="changeRole(user, $event.target.value)">
                <option v-for="role in roles" :value="role.value" >{{ role.name }}</option>
            </select>
        </td>
        <td>
            <a href="#" @click.prevent="$emit('editUser', user)"><i class="fa fa-edit"></i></a>
            <a href="#" @click.prevent="$emit('confirmUserDeletion', user.id)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>
