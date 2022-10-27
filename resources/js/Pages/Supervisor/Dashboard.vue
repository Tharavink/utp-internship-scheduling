<template>
    <app-layout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Supervisor Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-12xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200 sm:px-15">
                        <div class="antialiased sans-serif">
                            <create :user_index_route="user_index_route" @cancel-create="cancelCreate" @add-supervisor="addSupervisor" />
                            <hr class="mt-2">
                            <data-table :headings_prop="headings" :rows_prop.sync="supervisor_data" :selectedRows_prop.sync="selectedRows" :bulkActions_prop.sync="bulkActions" :filters_prop.sync="filters" :actions="tableActions" @updateSelectedRows="updateSelectedRows" @rowUpdated="rowUpdated" @actionTriggered="actionTriggered" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../../Layouts/AppLayout'
    import DataTable from './../../Components/DataTable'
    import ThemeButton from './../../Components/Button'
    import Create from './Create'

    import _ from "lodash"

    export default {
        props: ['user_index_route'],
        components: {
            AppLayout,
            DataTable,
            ThemeButton,
            Create
        },
        data() {
            return {
                supervisors: [],
                open: false,
                selectedRows: [],
                headings: [
                    {
                        'key': 'id',
                        'value': 'Supervisor ID',
                        'hidden': true
                    },
                    {
                        'key': 'name',
                        'value': 'Name'
                    },
                    {
                        'key': 'email',
                        'value': 'Email'
                    },
                ],
                tableActions: [
                    {
                        name: 'delete',
                        display: 'Delete',
                        look: 'error'
                    }
                ],
                bulkActions: [
                    
                ],
                filters: [
                    {
                        type: 'search',
                        label: 'Keyword',
                        placeholder: 'Search...',
                        name: 'keyword',
                        value: null
                    }
                ]
            }
        },
        mounted() {
            this.fetchSupervisors();
        },
        watch: {
            params: _.debounce(function() {
                this.fetchSupervisors();
            }, 500),
            bulkActions: {
                handler: function(val) {
                    this.applyAction(val);
                },
                deep: true
            }
        },
        computed: {
            supervisor_data: function() {
                let self = this;
                return this.supervisors.map(supervisor => {
                    return {
                        id: {
                            value: supervisor.id,
                            display: supervisor.id
                        },
                        name: {
                            value: supervisor.name,
                            display: supervisor.name
                        },
                        email: {
                            value: supervisor.email,
                            display: supervisor.email
                        }
                    }
                });
            },
            params: function() {
                let obj= {};
                this.filters.forEach(filter => {
                    obj[filter.name] = filter.value;
                });
                return obj;
            }
        },
        methods: {
            fetchSupervisors: function() {
                let self = this;
                axios.get(this.user_index_route, {
                    params: this.params
                })
                    .then(response => {
                        self.supervisors = response.data
                    });
            },
            rowUpdated: function(val) {
                let supervisor = Object.fromEntries(Object.entries(val).map(([ key, obj ]) => [ key, obj.value ]));
                this.updateSupervisor(supervisor);
            },
            applyAction: function(actions) {
                let supervisors = this.supervisors.filter(supervisor => this.selectedRows.includes(supervisor.id));
                let self = this;
                supervisors.forEach(supervisor => {
                    actions.forEach(action => {
                        if (action.value) {
                            supervisor[action.name] = action.value
                        }
                    });
                    self.updateSupervisor(supervisor);
                });
            },
            updateSupervisor: function(supervisor) {
                let self = this;
                axios.put(this.user_index_route + '/' + supervisor.id, { supervisor })
                    .then(response => {
                        self.fetchSupervisors();
                    })
                    .catch(error => {
                        if (error.response.data.message) {
                            self.fetchSupervisors();
                        }
                    })
            },
            updateSelectedRows: function(selectedRows) {
                this.selectedRows = selectedRows;
            },
            cancelCreate: function() {
                this.open = false;
            },
            addSupervisor: function(supervisor) {
                this.supervisors.unshift(supervisor);
            },
            toggleCreate: function() {
                this.open = true;
            },
            actionTriggered: function(action, idx) {
                let self = this;
                let supervisor = this.supervisors[idx];
                if (action == 'delete') {
                    this.$swal.fire({
                        title: 'Are you sure?',
                        text: "Deleting supervisor account cannot be reverted and the email cannot be reused for new user!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete!',
                        cancelButtonText: 'No, Go Back'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.delete(`${self.user_index_route}/${supervisor.id}`)
                                .then(response => {
                                    if (response.data.error) {
                                        self.$swal('Error!', response.data.error, 'error')
                                    } else {
                                        self.$swal('Success!', response.data.message, 'success')
                                        self.supervisors.splice(idx, 1)
                                    }
                                })
                        }
                    })
                    
                }
            }
        }
    }
</script>
