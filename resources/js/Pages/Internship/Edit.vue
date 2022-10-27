<template>
    <app-layout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Internship
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-12xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200 sm:px-15">
                        <div class="antialiased sans-serif">
                            <div class="flex flex-col items-center justify-center w-full py-2">
                                <h2 class="w-full px-2 pb-3 text-2xl text-center">Enroling to {{ term.name }}</h2>
                                
                                <div class="w-2/5 px-2 py-3">
                                    <label for="title" class="px-2 pb-3 text-xl">Internship Term</label>
                                    <div class="flex flex-row">
                                        <label class="inline-flex items-center mt-3 pr-10">
                                            <input type="radio" class="form-radio h-5 w-5 text-teal-600" name="internship_term" :value="1" v-model="internship.intern_term">
                                            <span class="ml-2 text-gray-700 text-lg">Student Internship Training (SIT)</span>
                                        </label>

                                        <label class="inline-flex items-center mt-3">
                                            <input type="radio" class="form-radio h-5 w-5 text-teal-600" name="internship_term" :value="2" v-model="internship.intern_term">
                                            <span class="ml-2 text-gray-700 text-lg">Student Internship Project (SIP)</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="w-2/5 px-2 py-3">
                                    <label for="title" class="px-2 pb-3 text-xl">{{ internship.intern_term == 1 ? 'Company Name' : 'Project Title' }}</label>
                                    <text-input type="text" :value="internship.title" @input="updateValue('title', $event)" :placeholder="internship.intern_term == 1 ? 'Type company name here...' : 'Type your title here...'" />
                                </div>

                                <div class="w-2/5 px-2 py-3">
                                    <label for="title" class="px-2 pb-3 text-xl">{{ internship.intern_term == 1 ? 'Company' : 'Project' }} Description</label>
                                    <text-input type="textarea" :value="internship.description" @input="updateValue('description', $event)" :placeholder="internship.intern_term == 1 ? 'Type company description here...' : 'Type your description here...'" />
                                </div>

                                <div class="w-2/5 px-2 py-3">
                                    <label for="host_email" class="px-2 pb-3 text-xl">Host Supervisor Email</label>
                                    <text-input type="text" :value="internship.host_email" @input="updateValue('host_email', $event)" placeholder="Type your host company supervisor email here..." />
                                </div>

                                <div class="w-2/5 px-2 py-3">
                                    <label for="student_contact" class="px-2 pb-3 text-xl">Student Contact Number</label>
                                    <text-input type="text" :value="internship.student_contact" @input="updateValue('student_contact', $event)" placeholder="Contact Number (eg. 01234561748)" />
                                </div>

                                <div class="w-2/5 px-2 py-3">
                                    <label for="host_address" class="px-2 pb-3 text-xl">Host Company Address</label>
                                    <text-input type="text" :value="internship.host_address" @input="updateValue('host_address', $event)" placeholder="Type company address here..." />
                                </div>

                                <theme-button v-if="!processing" type="button" look="success" :disabled="!canSubmit" @pressed="saveInternship">
                                    <div class="pr-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                                    </div>
                                    Submit
                                </theme-button>
                                <label v-else>Processing...</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from './../../Layouts/AppLayout'
import TextInput from '../../Components/TextInput'
import ThemeButton from '../../Components/Button'

export default {
    props: ['internship_index_route', 'term_index_route', 'term_id'],
    components: {
        AppLayout,
        TextInput,
        ThemeButton
    },
    data() {
        return {
            internship: {
                title: null,
                description: null,
                host_email: null,
                student_contact: null,
                host_address: null,
                intern_term: 1,
                term_id: this.term_id
            },
            term: {
                name: null
            },
            processing: false
        }
    },
    mounted() {
        this.fetchTermInternship(this.term_id)
    },
    computed: {
        canSubmit: function() {
            let reg = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/
            return this.internship.title != null && this.internship.description != null && reg.test(this.internship.host_email) && this.internship.host_address != null
        }
    },
    methods: {
        fetchTermInternship: function(term_id) {
            let self = this
            axios.get(`${this.term_index_route}/${term_id}`)
                .then(response => {
                    self.term = response.data.term
                    if (response.data.internship) {
                        self.internship = response.data.internship
                    }
                })
        },
        updateValue: function(field, val) {
            this.$set(this.internship, field, val)
        },
        saveInternship: function() {
            if (!/^\d+$/.test(this.internship.student_contact)) {
                this.$swal('Error!', 'Contact number field only allow digits', 'error');
                return
            }

            let self = this
            this.processing = true
            axios.post(this.internship_index_route, this.internship)
                .then(response => {
                    if (response.data.error) {
                        self.$swal('Error!', response.data.error, 'error');
                        self.processing = false
                    } else {
                        self.$swal({
                            icon: 'success',
                            title: 'Success!',
                            text: response.data.message,
                            confirmButtonText: 'OK, Great!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                self.processing = false
                                window.location.replace(`${self.internship_index_route}/detail/${response.data.internship.id}`);
                            }
                        })
                    }
                })
        }
    }
}
</script>