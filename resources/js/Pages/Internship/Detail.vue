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
                                <h2 v-if="internship.term" class="w-full px-2 pb-5 text-2xl text-center">Internship for term {{ internship.term.name }}</h2>

                                <div class="flex flex-row items-start justify-center w-full pt-5">
                                    <div class="flex flex-col items-start justify-center w-1/2 px-2">
                                        <div class="flex flex-col w-full py-2 border-b-4 border-gray-400 border-dashed">
                                            <div class="flex flex-row items-end justify-between pb-5">
                                                <div class="flex flex-row items-end justify-center pb-5">
                                                    <label for="details" class="text-xl text-center pr-5"><u>{{ internship.intern_term == 1 ? 'Company' : 'Project' }} Details</u></label>
                                                    <label for="details" :class="['text-lg text-center rounded-xl text-white py-2 px-4', internship.intern_term == 1 ? 'bg-indigo-700' : 'bg-green-700']">{{ internship.intern_term == 1 ? 'SIT' : 'SIP' }}</label>
                                                </div>
                                                <theme-button v-if="internship.id && internship.student_id == user.id && !detailEditing" type="button" look="info" @pressed="editPressed">
                                                    <div class="pr-3">
                                                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                          <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                        </svg>
                                                    </div>
                                                    Edit
                                                </theme-button>
                                                <theme-button v-if="internship.id && internship.student_id == user.id && detailEditing" :disabled="!canSubmitDetail" type="button" look="success" @pressed="saveInternship">
                                                    <div class="pr-3">
                                                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                                        </svg>
                                                    </div>
                                                    Save
                                                </theme-button>
                                            </div>

                                            <div v-if="detailEditing" class="flex flex-col items-start justify-start">
                                                <div class="w-full px-2 py-3">
                                                    <label for="title" class="px-2 pb-3 text-xl">{{ internship.intern_term == 1 ? 'Company Name' : 'Project Title' }}</label>
                                                    <text-input type="text" :value="internship.title" @input="updateValue('title', $event)" :placeholder="internship.intern_term == 1 ? 'Type company name here...' : 'Type your title here...'" />
                                                </div>

                                                <div class="w-full px-2 py-3">
                                                    <label for="title" class="px-2 pb-3 text-xl">{{ internship.intern_term == 1 ? 'Company' : 'Project' }} Description</label>
                                                    <text-input type="textarea" :value="internship.description" @input="updateValue('description', $event)" :placeholder="internship.intern_term == 1 ? 'Type company description here...' : 'Type your description here...'" />
                                                </div>

                                                <div class="w-full px-2 py-3">
                                                    <label for="host_email" class="px-2 pb-3 text-xl">Host Supervisor Email</label>
                                                    <text-input type="text" :value="internship.host_email" @input="updateValue('host_email', $event)" placeholder="Type your host company supervisor email here..." />
                                                </div>

                                                <div class="w-full px-2 py-3">
                                                    <label for="student_contact" class="px-2 pb-3 text-xl">Student Contact</label>
                                                    <text-input type="text" :value="internship.student_contact" @input="updateValue('student_contact', $event)" placeholder="Contact Number (eg. 01234561748)" />
                                                </div>

                                                <div class="w-full px-2 py-3">
                                                    <label for="host_address" class="px-2 pb-3 text-xl">Host Company Address</label>
                                                    <text-input type="text" :value="internship.host_address" @input="updateValue('host_address', $event)" placeholder="Type company address here..." />
                                                </div>
                                            </div>
                                            <div v-else class="flex flex-col items-start justify-start">
                                               <label for="title" class="text-xl font-bold">{{ internship.title }}</label>
                                                <label for="description" class="pb-3 text-lg">{{ internship.description }}</label>
                                                <label v-if="internship.host_email" for="host_email" class="pb-3 text-lg">Host Supervisor Email : {{ internship.host_email }}</label>
                                                <label v-if="internship.student_contact" for="student_contact" class="pb-3 text-lg">Student Contact : {{ internship.student_contact }}</label>
                                                <label v-if="internship.host_address" for="host_address" class="pb-3 text-lg">
                                                    Host Company Address : <a :href="`https://maps.google.com/?q=${internship.host_address}`" target="_blank" class="text-blue-600 underline">{{ internship.host_address }}</a>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="flex flex-col w-full py-2">
                                            <label for="title" class="pt-3 text-lg">Supervisor : {{ internship.supervisor ? internship.supervisor.name : 'Not Assigned' }}</label>
                                            <label v-if="internship.scheduled" for="description" class="pb-3 text-lg">Visit : {{ internship.scheduled | moment("dddd, MMMM Do YYYY, h:mm a") }}</label>
                                            <label v-else for="description" class="pb-3 text-lg">Visit : Not Scheduled</label>

                                            <div v-if="internship.scheduled && internship.is_completed == 0" class="flex flex-col items-end justify-end w-full">
                                                <theme-button v-if="user.role_id == 2 && user.id == internship.supervisor_id && !detailEditing" type="button" look="success" @pressed="completeInternship">
                                                    <div class="pr-3">
                                                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                    Complete Visit
                                                </theme-button>
                                            </div>
                                            <label v-else-if="internship.is_completed == 1" for="description" class="pb-3 text-xl text-white bg-green-500 text-center p-3">Visit Completed</label>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-start justify-center w-1/2 px-2">
                                        <div v-if="user.role_id == 1" class="flex flex-col w-full py-2">
                                            <label for="details" class="w-full px-2 pb-5 text-xl text-center"><u>Assign Supervisor</u></label>
                                            <div class="flex flex-row items-center justify-center">
                                                <text-input type="text" :value="filter.name" @input="updateFilterValue('keyword', $event)" placeholder="Filter selection by name or email..." tailwindStyle="mr-2" />
                                                <select-box :value_prop="internship.supervisor_id" :options_props="supervisors" :placeholder="'Select Supervisor'" @valueUpdated="updateValue('supervisor_id', $event)" />
                                            </div>
                                        </div>
                                        <div v-else-if="user.role_id == 2 && user.id == internship.supervisor_id" class="flex flex-col items-end w-full py-2">
                                            <label for="details" class="w-full px-2 pb-5 text-xl text-center"><u>Schedule Visit</u></label>
                                            <flat-pickr v-model="date" :config="config" placeholder="Select a date for the visit" name="visitdate" class="w-full p-3 mb-3 text-xl text-center border-4 border-indigo-500 rounded-md"></flat-pickr>
                                            <theme-button type="button" look="success" :disabled="!canSubmitSchedule" @pressed="updateValue('scheduled', date)">
                                                <div class="pr-3">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                                                </div>
                                                Submit Visit Time
                                            </theme-button>
                                        </div>
                                        <div v-else-if="user.role_id == 3 && user.id == internship.student_id && internship.scheduled" class="flex flex-col items-end w-full py-2">
                                            <label for="details" class="w-full px-2 pb-5 text-xl text-center"><u>Request To Reschedule</u></label>

                                            <div class="flex flex-row items-center justify-center w-full">
                                                <div class="w-2/5 px-2 py-3">
                                                    <label for="title" class="px-2 pb-3 text-xl">Reschedule To</label>
                                                    <flat-pickr v-model="request.requested" :config="config" placeholder="Select a date to reshedule" name="rescheduledate" class="w-full rounded-md shadow-sm form-input"></flat-pickr>
                                                </div>
                                                <div class="w-3/5 px-2 py-3">
                                                    <label for="title" class="px-2 pb-3 text-xl">Reason To Reschedule</label>
                                                    <text-input type="text" :value="request.reason" @input="updateRequestValue('reason', $event)" placeholder="Type your reason here..." />
                                                </div>
                                            </div>
                                            
                                            <theme-button type="button" look="success" :disabled="!canSubmitRequest" @pressed="submitReschedule()" tailwindStyle="mr-2">
                                                <div class="pr-3">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                                                </div>
                                                Submit Request
                                            </theme-button>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-start justify-center w-full pt-5 mt-5 border-t-4 border-gray-400 border-dashed">
                                    <label for="details" class="w-full px-2 pb-5 text-xl text-center"><u>Reschedule Requests</u></label>
                                    
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Requested By
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Reschedule To
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Reason
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Submitted On
                                                </th>
                                                <th v-if="user.role_id == 2 && user.id == internship.supervisor_id" scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="internship.requests && internship.requests.length > 0" class="bg-white divide-y divide-gray-200">
                                            <tr v-for="(request, req_idx) in internship.requests" :key="`request_${request.id}`">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ request.requestor.name }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ request.requested | moment("dddd, MMMM Do YYYY, h:mm a") }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ request.reason }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ request.created_at | moment("dddd, MMMM Do YYYY, h:mm a") }}</div>
                                                </td>
                                                <td v-if="user.role_id == 2 && user.id == internship.supervisor_id" class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                                    <button v-if="req_idx == internship.requests.length - 1 && request.requested != internship.scheduled" type="button" class="text-indigo-600 hover:text-indigo-900" @click="updateValue('scheduled', request.requested)">Accept Request</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tbody v-else class="bg-white divide-y divide-gray-200">
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap" colspan="5">
                                                    <div class="text-sm text-gray-900">No reschedule requests submitted</div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
import SelectBox from '../../Components/SelectBox.vue'

import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import moment from 'moment'
import _ from "lodash"

export default {
    props: ['internship_index_route', 'user_index_route', 'reschedule_index_route', 'internship_id', 'user'],
    components: {
        AppLayout,
        TextInput,
        ThemeButton,
        SelectBox,
        flatPickr
    },
    data() {
        return {
            internship: {},
            date: null,
            detailEditing: false,
            supervisors: [],
            filter: {
                role_id: 2,
                keyword: null
            },
            config: {
                minDate: "today",
                altFormat: "F j, Y | H:i",
                altInput: true,
                enableTime: true
            },
            request: {
                requested: null,
                reason: ''
            }
        }
    },
    mounted() {
        this.fetchInternship(this.internship_id)
        if (this.user.role_id == 1) {
            this.fetchSupervisors()
        }
    },
    watch: {
        "filter.keyword": _.debounce(function() {
            this.fetchSupervisors()
        }, 500)
    },
    computed: {
        canSubmitSchedule: function() {
            return this.date != null
        },
        canSubmitRequest: function() {
            return this.request.requested != null && this.request.reason != ''
        },
        canSubmitDetail: function() {
            let reg = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/
            return this.internship.title != '' && this.internship.description != '' && reg.test(this.internship.host_email) && this.internship.host_address != ''
        }
    },
    methods: {
        fetchInternship: function(internship_id) {
            let self = this
            axios.get(`${this.internship_index_route}/${internship_id}`)
                .then(response => {
                    self.internship = response.data
                })
        },
        fetchSupervisors: function() {
            let self = this
            axios.get(`${this.user_index_route}`, {
                params: self.filter
            })
                .then(response => {
                    self.supervisors = response.data.map(supervisor => ({ value: supervisor.id, text: supervisor.name }))
                })
        },
        updateValue: function(field, val) {
            let self = this

            let curDate = this.internship.scheduled
            let text = null
            let success = null
            if (field == 'supervisor_id') {
                text = 'Are you sure to set supervisor for this internship!'
                success = 'Successfully set supervisor'
            }
            else if (field == 'scheduled') {
                this.internship.scheduled = val
                text = `Are you sure to set visit date and time as ${moment(this.internship.scheduled).format('MMMM DD, YYYY | hh:mm a')}!`
                success = 'Successfully set visit date'
            }

            if (text) {
                this.$swal.fire({
                        title: 'Are you sure?',
                        text,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Yes, proceed!',
                        cancelButtonText: 'No, Go Back'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            self.$set(self.internship, field, val)
                            axios.put(`${self.internship_index_route}/${self.internship.id}`, self.internship)
                                .then(response => {
                                    if (response.data.error) {
                                        self.$swal('Error!', response.data.error, 'error')
                                    } else {
                                        self.$swal('Success!', success, 'success')
                                        self.internship = response.data
                                    }
                                })
                        } else {
                            self.$set(self.internship, 'scheduled', curDate)
                        }
                    })
            } else {
                this.$set(this.internship, field, val)
            }
        },
        updateFilterValue: function(field, val) {
            this.$set(this.filter, field, val)
        },
        updateRequestValue: function(field, val) {
            this.$set(this.request, field, val)
        },
        submitReschedule: function() {
            let self = this
            axios.post(`${this.reschedule_index_route}`, { ...this.request, internship_id: this.internship.id, requested_by_id: this.user.id })
                .then(response => {
                    self.request = {
                        requested: null,
                        reason: ''
                    }
                    self.$swal('Success!', 'Reschedule request sent successfully', 'success')
                    self.internship.requests.push(response.data)
                })
        },
        editPressed: function() {
            this.detailEditing = true
        },
        saveInternship: function() {
            if (this.detailEditing && !/^\d+$/.test(this.internship.student_contact)) {
                this.$swal('Error!', 'Contact number field only allow digits', 'error');
                return
            }
            
            let self = this
            axios.put(`${this.internship_index_route}/${this.internship.id}`, this.internship)
                .then(response => {
                    if (response.data.error) {
                        self.$swal('Error!', response.data.error, 'error')
                    } else {
                        self.$swal('Success!', 'Internship data updated successfully', 'success')
                        self.detailEditing = false
                        self.internship = response.data
                    }
                })
        },
        completeInternship: function() {
            this.internship.is_completed = 1
            this.saveInternship()
        }
    }
}
</script>