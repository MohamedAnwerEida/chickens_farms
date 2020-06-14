<template>
    <div class="content-header"  v-if="$gate.isAdmin">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Users</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Users Table</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-success" @click="newModel">
                                    Add New
                                    <i class="fa fa-user-plus" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users.data" :key="user.id ">
                                        <td>{{ user.id }}</td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.email }}</td>
                                        <td>{{ user.type }}</td>
                                        <td>{{ user.created_at | myDate }}</td>
                                        <td>
                                            <button type="button" class="btn btn-info"  @click="editModel(user)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button"  class="btn btn-danger" @click="deleteUser(user.id)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="crad-footer">
                            <pagination :data="users" @pagination-change-page="getResults"></pagination>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
        
        <div v-if="!$gate.isAdmin()">
            <not-found></not-found>
        </div>
        <!-- Add Modal -->
        <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" v-show="!editmode" id="addNewLabel">Add New</h5>
                <h5 class="modal-title" v-show="editmode" id="addNewLabel">Update User's Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form  @submit.prevent="editmode ? updateUser() : createUser()">
            <div class="modal-body">
                
                <div class="form-group">
                    <label>Name</label>
                    <input v-model="form.name" type="text" name="name"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                    <has-error :form="form" field="name"></has-error>
                </div>
                <div class="form-group">
                    <label>email</label>
                    <input v-model="form.email" type="email" name="email"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                    <has-error :form="form" field="email"></has-error>
                </div>
                <div class="form-group">
                    <label>phone</label>
                    <input v-model="form.phone" type="text" name="phone"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('phone') }">
                    <has-error :form="form" field="phone"></has-error>
                </div>
                <div class="form-group">
                    <label for="type">type</label>
                    <select v-model="form.type" class="form-control" name="type" id="type" :class="{ 'is-invalid': form.errors.has('type') }">
                    <option value="">Choose..</option>
                    <option value="Admin">Admin</option>
                    <option value="Player">Player</option>
                    <option value="Company">Company</option>
                    <option value="Pitche">Pitche</option>
                    <option value="Data Entry">Data Entry</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>password</label>
                    <input v-model="form.password" type="password" name="password" class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                    <has-error :form="form" field="password"></has-error>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
            </div>
            </form>
            </div>
        </div>
        </div>
    </div>

</template>
<style scoped> .pagination { justify-content: center!important; } </style>
<script>
    export default {
        data () {
            return {
                editmode: false,
                users : {},
                // Create a new form instance
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    phone: '',
                    type: '',
                    password: ''
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/dashboard/user?page=' + page)
                    .then(response => {
                        this.users = response.data;
                    });
            },
            updateUser(){
                this.$Progress.start();
                // console.log('Editing data');
                this.form.put('/dashboard/user/'+this.form.id)
                .then(() => {
                    // success
                    $('#add').modal('hide');
                    Swal.fire(
                        'Updated!',
                        'Information has been updated.',
                        'success'
                    )
                    this.$Progress.finish();
                    Fire.$emit('AfterCreate');
                })
                .catch(() => {
                    this.$Progress.fail();
                });
            },
            newModel () { 
                this.editmode = false;
                this.form.reset();
                $('#add').modal('show');
            },
            editModel (user) { 
                this.editmode = true;
                this.form.reset();
                this.form.fill(user);
                $('#add').modal('show');
            },
            deleteUser(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        // send POST request to delete
                        this.form.delete('/dashboard/user/' + id).then(() => {
                            Fire.$emit('AfterCreate');
                            Swal.fire(
                                'Deleted!',
                                'User has been deleted.',
                                'success'
                            )
                        }).catch(()=> {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!'
                            })
                        });
                    }
                })
            },
            create () {
                this.$Progress.start();
                // Submit the form via a POST request
                this.form.post('/dashboard/user')
                .then(() => {
                    Fire.$emit('AfterCreate');
                    $('#add').modal('hide');
                    Toast.fire({
                        icon: 'success',
                        title: 'User Created successfully'
                    });
                    this.$Progress.finish();
                }).catch(()=> {
                    this.$Progress.fail();
                    /*
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!'
                    })*/
                });
            },
            loadUsers () {
                if (this.$gate.isAdmin()) {
                    // get all users
                    axios.get('/dashboard/user').then(({data}) => (this.users = data))
                }

            }
        },
        created() {
            Fire.$on('searching',() => {
                let query = this.$parent.search;                
                axios.get('/dashboard/search?q=' + query)
                .then((data) => {
                     this.users = data.data;
                })
                .catch(() => {
                })
            })
            this.loadUsers();
            //setInterval(() => this.loadUsers(), 3000);
            Fire.$on('AfterCreate',() => {
               this.loadUsers();
           });
        }
    }

</script>
