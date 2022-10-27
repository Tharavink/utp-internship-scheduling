<template>
    <div class="flex flex-col items-start justify-center w-full py-2">
        <div class="flex flex-row items-end justify-center">
            <h2 class="px-2 text-2xl pb-3 w-1/2">Create New Term: </h2>

            <div class="w-3/5 px-2">
                <label for="name" class="pb-3 text-xl px-2">Name of Term</label>
                <text-input type="text" :value="term.name" @input="updateValue('name', $event)" placeholder="(eg. Sept 2020)..." />
            </div>

            <theme-button v-if="!processing" type="button" look="dark" slot="button" tailwindStyle="mr-2" @pressed="cancel">
                <div class="pr-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                Clear
            </theme-button>
            <theme-button v-if="!processing" type="button" look="success" :disabled="!canSubmit" @pressed="saveTerm">
                <div class="pr-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                </div>
                Submit
            </theme-button>
            <label v-else>Processing...</label>
        </div>
    </div>
</template>

<script>
import TextInput from '../../Components/TextInput'
import ThemeButton from '../../Components/Button'

export default {
    props: ['term_index_route'],
    components: {
        TextInput,
        ThemeButton
    },
    data() {
        return {
            term: {
                name: null
            },
            processing: false
        }
    },
    computed: {
        canSubmit: function() {
            return this.term.name != null
        }
    },
    methods: {
        updateValue: function(field, val) {
            this.$set(this.term, field, val)
        },
        saveTerm: function() {
            let self = this
            this.processing = true
            axios.post(this.term_index_route, this.term)
                .then(response => {
                    if (response.data.error) {
                        self.$swal('Error!', response.data.error, 'error');
                    } else {
                        self.term = {
                            name: null
                        }
                        self.$swal('Success!', `${response.data.name} have been created successfully`, 'success');
                        self.processing = false
                        self.$emit('add-term', response.data)
                    }
                })
        },
        cancel: function() {
            this.term = {
                name: null,
            }
            this.$emit('cancel-create')
        }
    }
}
</script>