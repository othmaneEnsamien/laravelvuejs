<script setup>
import axios from 'axios';
import { ref, onMounted, reactive, watch } from 'vue';
import { Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import UserListItem from './UserListItem.vue';
import { debounce } from 'lodash';



const toastr = useToastr();
const users = ref([]);
const editing = ref(false);
const formValues = ref();
const form = ref(null);




const getUsers = () => {
    axios.get('/api/users')
        .then(response => {
           users.value = response.data; // Utilisez users.value pour mettre à jour la valeur
        })
}

const createUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(8),
});

const editUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().when((password, schema) => {
        return password ? schema.required().min(8) : schema;
    }),
});

const createUser = (values, { setErrors }) => {
    axios.post('/api/users/', values)
        .then((response) => {
            users.value.unshift(response.data);
            $('#userFormModal').modal('hide');
            toastr.success('User created successfully!');
        })
       
};

const addUser = () => {
    editing.value = false;
    $('#userFormModal').modal('show');
};

const editUser = (user) => {
    editing.value = true;
    form.value.resetForm();
    $('#userFormModal').modal('show');
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email,
    };
};

const updateUser = (values) => {
    axios.put('/api/users/' + formValues.value.id, values)
        .then((response) => {
            const index = users.value.findIndex(user => user.id === response.data.id);
            users.value[index] = response.data;
            $('#userFormModal').modal('hide');
            toastr.success('User updated successfully!');
        }).catch((error) => {
            console.log(error);
        }).finally(
            () => { 
                form.value.resetForm();
            }
        );
}

const userDeleted 
    = (userId) => {
        users.value = users.value.filter(user => user.id !== userId);
    };

       


const handleSubmit = (values, actions) => {
    console.log('editing:', editing.value); // Ajoutez cette ligne
    console.log('formValues:', formValues.value); // Ajoutez cette ligne

    if (editing.value) {
        updateUser(values, actions);
    } else {
        createUser(values, actions);
    }
}
const Searchquery = ref(null);
const search = () => {
    axios.get('/api/users/search' , {
        params: {
            query: Searchquery.value
        }
    })
        .then(response => {
           users.value = response.data; // Utilisez users.value pour mettre à jour la valeur
        })
        .catch(
            (error) => {
                console.log(error);
            }
        )

}

watch(Searchquery, debounce(() => {
    search();
}, 300));


onMounted(() => {
    getUsers();
});
</script>
<template>
<div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">users</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">users</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
        <div class="d-flex justify-content-between">
                     <button @click="addUser" type="button" class="mb-2 btn btn-primary">
                        Add New User
                    </button>
                      <div>
                    <input type="text" v-model="Searchquery" class="form-control" placeholder="Search..." />
                </div>
        </div>
                <div class="card">
                    <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                         <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Created At</th>
                                    <th>Role</th>
                                    <th>role actuel</th>
                                    <th>Actions</th>
                                    </tr>
                                   
                                
                                </thead>
                                <tbody v-if="users.length > 0">
                                  <UserListItem v-for="(user, index) in users" 
                                  :key="user.id" 
                                  :user=user 
                                  :index=index
                                  @user-deleted="userDeleted"
                                  @edit-user="editUser"
                                   />
                                </tbody>
                                <tbody v-else>
                                       <tr>
                                <td colspan="12" class="text-center">No results found...</td>
                            </tr>
                                </tbody>
                            </table>
                        </div>
                </div>
                    
                
            
        </div>
        
        </div>

      <div class="modal fade" id="userFormModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit User</span>
                        <span v-else>Add New User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editUserSchema : createUserSchema"
                    v-slot="{ errors }" :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <Field name="name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }"
                                id="name" aria-describedby="nameHelp" placeholder="Enter full name" />
                            <span class="invalid-feedback">{{ errors.name }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <Field name="email" type="email" class="form-control "
                                :class="{ 'is-invalid': errors.email }" id="email" aria-describedby="nameHelp"
                                placeholder="Enter full name" />
                            <span class="invalid-feedback">{{ errors.email }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Password</label>
                            <Field name="password" type="password" class="form-control "
                                :class="{ 'is-invalid': errors.password }" id="password" aria-describedby="nameHelp"
                                placeholder="Enter password" />
                            <span class="invalid-feedback">{{ errors.password }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>
      
</div>

    

</template>