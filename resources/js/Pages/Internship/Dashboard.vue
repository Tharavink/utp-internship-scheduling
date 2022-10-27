<template>
    <app-layout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Internship Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-12xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200 sm:px-15">
                        <div class="antialiased sans-serif">
                            <data-table :headings_prop="headings" :rows_prop.sync="internship_data" :selectedRows_prop.sync="selectedRows" :bulkActions_prop.sync="bulkActions" :filters_prop.sync="filters" :actions="tableActions" @updateSelectedRows="updateSelectedRows" @rowUpdated="rowUpdated" @actionTriggered="actionTriggered" />
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

    import _ from "lodash"

    export default {
        props: ['internship_index_route', 'user'],
        components: {
            AppLayout,
            DataTable,
            ThemeButton
        },
        data() {
            return {
                internships: [],
                open: false,
                selectedRows: [],
                headings: [
                    {
                        'key': 'id',
                        'value': 'Internship ID',
                        'hidden': true
                    },
                    {
                        'key': 'term_id',
                        'value': 'Term'
                    },
                    {
                        'key': 'title',
                        'value': 'Title'
                    },
                    {
                        'key': 'intern_term',
                        'value': 'Internship Term'
                    },
                    {
                        'key': 'is_completed',
                        'value': 'Status'
                    }
                ],
                tableActions: this.user.role_id == 1 ? [
                    {
                        name: 'delete',
                        display: 'Delete',
                        look: 'error'
                    },
                    {
                        name: 'detail',
                        display: 'Detail',
                        look: 'info'
                    }
                ] : [
                    {
                        name: 'detail',
                        display: 'Detail',
                        look: 'info'
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
                    },
                    {
                        type: 'select',
                        label: 'Internship Term',
                        placeholder: 'Choose Internship Term...',
                        name: 'intern_term',
                        options: [
                            { text: 'SIT', value: 1 },
                            { text: 'SIP', value: 2 }
                        ],
                        value: null
                    },
                    {
                        type: 'select',
                        label: 'Visit Status',
                        placeholder: 'Choose Visit Status...',
                        name: 'is_completed',
                        options: [
                            { text: 'Pending', value: 0 },
                            { text: 'Completed', value: 1 }
                        ],
                        value: null
                    }
                ]
            }
        },
        mounted() {
            this.fetchInternships();
        },
        watch: {
            params: _.debounce(function() {
                this.fetchInternships();
            }, 500),
            bulkActions: {
                handler: function(val) {
                    this.applyAction(val);
                },
                deep: true
            }
        },
        computed: {
            internship_data: function() {
                let self = this;
                return this.internships.map(internship => {
                    return {
                        id: {
                            value: internship.id,
                            display: internship.id
                        },
                        term_id: {
                            value: internship.term_id,
                            display: internship.term.name
                        },
                        title: {
                            value: internship.title,
                            display: internship.title
                        },
                        intern_term: {
                            value: internship.intern_term,
                            display: internship.intern_term == 1 ? 'SIT' : 'SIP'
                        },
                        is_completed: {
                            value: internship.is_completed,
                            display: internship.is_completed ? 'Completed' : 'Pending',
                            cssClass: internship.is_completed ? 'bg-green-500' : 'bg-yellow-400'
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
            fetchInternships: function() {
                let self = this;
                axios.get(this.internship_index_route, {
                    params: this.params
                })
                    .then(response => {
                        self.internships = response.data
                    });
            },
            rowUpdated: function(val) {
                let internship = Object.fromEntries(Object.entries(val).map(([ key, obj ]) => [ key, obj.value ]));
                this.updateInternship(internship);
            },
            applyAction: function(actions) {
                let internships = this.internships.filter(internship => this.selectedRows.includes(internship.id));
                let self = this;
                internships.forEach(internship => {
                    actions.forEach(action => {
                        if (action.value) {
                            internship[action.name] = action.value
                        }
                    });
                    self.updateInternship(internship);
                });
            },
            updateInternship: function(internship) {
                let self = this;
                axios.put(this.internship_index_route + '/' + internship.id, { internship })
                    .then(response => {
                        self.fetchInternships();
                    })
                    .catch(error => {
                        if (error.response.data.message) {
                            self.fetchInternships();
                        }
                    })
            },
            updateSelectedRows: function(selectedRows) {
                this.selectedRows = selectedRows;
            },
            actionTriggered: function(action, idx) {
                let self = this;
                let internship = this.internships[idx];
                if (action == 'detail') {
                    window.location.replace(`${self.internship_index_route}/detail/${internship.id}`);
                }
                else if (action == 'delete') {
                    this.$swal.fire({
                        title: 'Are you sure?',
                        text: "Deleting internship cannot be reverted!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Yes, delete!',
                        cancelButtonText: 'No, Go Back'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.delete(`${self.internship_index_route}/${internship.id}`)
                                .then(response => {
                                    if (response.data.error) {
                                        self.$swal('Error!', response.data.error, 'error')
                                    } else {
                                        self.$swal('Success!', response.data.message, 'success')
                                        self.internships.splice(idx, 1)
                                    }
                                })
                        }
                    })
                }
            }
        }
    }
</script>