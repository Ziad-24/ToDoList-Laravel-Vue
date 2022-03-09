<template>
    <div class="container w-50">
        <form @submit.prevent="saveData">
            <div class="input-group mb-3 w-100">
           
                <input
                    v-model="form.title"
                    type="text"
                    class="form-control"
                    placeholder="Add new Todo to the list"
                    aria-label="Todo"
                    aria-describedby="basic-addon2"
                    :class="{ 'is-invalid': form.errors.has('title') }"
                    @keydown="form.errors.clear('title')"
                />
                <input type="text" v-model="form.userid" style="display:none;"/>

                <div class="input-group-append">
                    <button class="btn btn-success" type="submit">
                        Add item to list
                    </button>
                </div>
            </div>
            <span
                class="text-danger"
                style="font-size: 20px"
                v-if="form.errors.has('title')"
                v-text="form.errors.get('title')"
            ></span>
        </form>

       <div class="w-100 todo">
            <div v-for="todo in todos" :key="todo.id" class="w-100 d-flex align-items-center p-3 bg-white border-bottom justify-content-between">
               <div class="d-flex align-items-baseline"> 
                    <span class="mr-2">
                        <svg @click="toggleTodo(todo)" v-if="todo.completed == false" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle" width="36" height="36" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FFC107" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z"/>
                            <circle cx="12" cy="12" r="9" />
                        </svg> 
                        <svg v-if="todo.completed == true" @click="toggleTodo(todo)" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-check" width="36" height="36" viewBox="0 0 24 24" stroke-width="1.5" stroke="#4CAF50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <circle cx="12" cy="12" r="9" />
                        <path d="M9 12l2 2l4 -4" />
                        </svg>
                        </span>

                    <div  class="font-weight-bolder">
                        <span v-if="editmode == false || editmode != todo.id">{{todo.title}}</span>
                        <input v-if="editmode == todo.id"   v-model="todo.title" type="text">
                    </div>
               </div>
                <div class="ml-auto mr-2 d-flex align-items-center">
                    <span>

                        <svg @click="editmode = todo.id"  v-if="editmode != todo.id" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="36" height="36" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FFC107" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                        <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                        <line x1="16" y1="5" x2="19" y2="8" />
                        </svg>


                        <svg v-if="editmode == todo.id" @click="updateTodo(todo)"  xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-checkbox" width="36" height="36" viewBox="0 0 24 24" stroke-width="1.5" stroke="#4CAF50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <polyline points="9 11 12 14 20 6" />
                        <path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" />
                        </svg>
                        </span>

                        <span>
                            <svg @click="deleteTodo(todo)" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash ml-1" width="36" height="36" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FF5722" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <line x1="4" y1="7" x2="20" y2="7" />
                        <line x1="10" y1="11" x2="10" y2="17" />
                        <line x1="14" y1="11" x2="14" y2="17" />
                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                        </svg>
                    </span>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['userId'],
    data() {
        return {
            form: new Form({
                title: "",
                userid: this.userId,
            }),
            todos: "",
            editmode : false, 
        };
    },
    // on every load
    mounted() {
        this.getId();
        this.getData();
    },

    methods: {
        saveData() {
            let data = new FormData();
            data.append("title", this.form.title);
            data.append('user_id',this.getId());
            axios
                .post("todo/", data)
                .then((res) => {
                    this.form.reset();
                    this.getData();
                })
                .catch((error) => {
                    this.form.errors.record(error.response.data.errors);
                });
        },
        getData() {
            axios
                .get("todo")
                .then((res) => {
                    this.todos = res.data;
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        toggleTodo(e){
            e.completed = !e.completed;
            let data = new FormData();
            // an update event using patch
            data.append('_method' , 'PATCH');
            // send the updated data to the database
            if(e.completed == 1)
            {
                data.append('completed' , 1);
            }
            else{
                data.append('completed',0);
            }
            // e.completed ? data.append('completed' , 1) : data.append('completed' , 0);
            // update with the current data
            axios.post('todo/'+e.id , data);
        },
        updateTodo(e){
            this.editmode = false;
            let data = new FormData();
            data.append('_method','PATCH');
            data.append('title', e.title);
            axios.post('todo/'+e.id , data).then(res=>{

            }).catch(error=>{
                this.form.errors.record(error.response.data.errors);
            })
        },
        deleteTodo(e){
            this.editmode = false;
            let data = new FormData();
            data.append('_method','DELETE');
            axios.post('todo/'+e.id , data).then(res=>{
                this.todos = res.data;
            }).catch(error=>{
                this.form.errors.record(error.response.data.errors);
            });
        },
        getId(){
            console.log(this.userId);
            return this.userId;
        },

    },
};
</script>
