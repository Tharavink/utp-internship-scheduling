<template>
    <app-layout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Term Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-12xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200 sm:px-15">
                        <div class="antialiased sans-serif">
                            <create :term_index_route="term_index_route" @cancel-create="cancelCreate" @add-term="addTerm" />
                            <hr class="mt-2">
                            <data-table :headings_prop="headings" :rows_prop.sync="term_data" :selectedRows_prop.sync="selectedRows" :bulkActions_prop.sync="bulkActions" :filters_prop.sync="filters" :actions="tableActions" @updateSelectedRows="updateSelectedRows" @rowUpdated="rowUpdated" @actionTriggered="actionTriggered" />
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
        props: ['user', 'term_index_route', 'internship_index_route'],
        components: {
            AppLayout,
            DataTable,
            ThemeButton,
            Create
        },
        data() {
            return {
                terms: [],
                open: false,
                selectedRows: [],
                headings: [
                    {
                        'key': 'id',
                        'value': 'Term ID',
                        'hidden': true
                    },
                    {
                        'key': 'name',
                        'value': 'Name'
                    }
                ],
                tableActions: this.user.role_id == 1 ? [
                    {
                        name: 'delete',
                        display: 'Delete',
                        look: 'error',
                    }
                ] : (
                    this.user.role_id == 3 ? [
                        {
                            name: 'enrol',
                            display: 'Enrol',
                            look: 'success',
                            ignoreIf: {
                                name: 'enrolled',
                                values: [true],
                                display: 'Enrolled'
                            }
                        }
                    ] : []
                ),
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
            this.fetchTerms();
        },
        watch: {
            params: _.debounce(function() {
                this.fetchTerms();
            }, 500),
            bulkActions: {
                handler: function(val) {
                    this.applyAction(val);
                },
                deep: true
            }
        },
        computed: {
            term_data: function() {
                let self = this;
                return this.terms.map(term => {
                    return {
                        id: {
                            value: term.id,
                            display: term.id
                        },
                        name: {
                            value: term.name,
                            display: term.name
                        },
                        enrolled: {
                            value: term.enrolled,
                            display: term.enrolled
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
            fetchTerms: function() {
                let self = this;
                axios.get(this.term_index_route, {
                    params: this.params
                })
                    .then(response => {
                        self.terms = response.data
                    });
            },
            rowUpdated: function(val) {
                let term = Object.fromEntries(Object.entries(val).map(([ key, obj ]) => [ key, obj.value ]));
                this.updateTerm(term);
            },
            applyAction: function(actions) {
                let fetchTerms = this.fetchTerms.filter(term => this.selectedRows.includes(term.id));
                let self = this;
                fetchTerms.forEach(term => {
                    actions.forEach(action => {
                        if (action.value) {
                            term[action.name] = action.value
                        }
                    });
                    self.updateTerm(term);
                });
            },
            updateTerm: function(term) {
                let self = this;
                axios.put(this.term_index_route + '/' + term.id, { term })
                    .then(response => {
                        self.fetchTerms();
                    })
                    .catch(error => {
                        if (error.response.data.message) {
                            self.fetchTerms();
                        }
                    })
            },
            updateSelectedRows: function(selectedRows) {
                this.selectedRows = selectedRows;
            },
            cancelCreate: function() {
                this.open = false;
            },
            addTerm: function(term) {
                this.terms.unshift(term);
            },
            toggleCreate: function() {
                this.open = true;
            },
            actionTriggered: function(action, idx) {
                let self = this;
                let term = this.terms[idx];
                if (action == 'delete') {
                    this.$swal.fire({
                        title: 'Are you sure?',
                        text: "Deleting term cannot be reverted!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete!',
                        cancelButtonText: 'No, Go Back'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.delete(`${self.term_index_route}/${term.id}`)
                                .then(response => {
                                    if (response.data.error) {
                                        self.$swal('Error!', response.data.error, 'error')
                                    } else {
                                        self.$swal('Success!', response.data.message, 'success')
                                        self.terms.splice(idx, 1)
                                    }
                                })
                        }
                    })
                } else if (action == 'enrol') {
                    window.open(`${this.internship_index_route}/enrol/${term.id}`, '_self');
                }
            }
        }
    }
</script>
