<template>
    <el-date-picker
        class="SharpDateRange"
        :class="classes"
        :value="transformedValue"
        :format="transformedFormat"
        :start-placeholder="startPlaceholder"
        :end-placeholder="endPlaceholder"
        :disabled="readOnly"
        :clearable="clearable"
        :picker-options="pickerOptions"
        type="daterange"
        popper-class="SharpDateRange__popper"
        ref="picker"
        @input="handleChanged"
        @focus="handleFocused"
        @blur="handleBlur"
    />
</template>

<script>
    import { lang } from "../../../../mixins/Localization";
    import ElDatePicker from 'element-ui/lib/date-picker';

    export default {
        components: {
            ElDatePicker,
        },
        props: {
            value: {
                default: () => ({
                    start: null,
                    end: null,
                }),
            },
            displayFormat: {
                type: String,
                default: 'DD/MM/YYYY',
            },
            startPlaceholder: {
                type: String,
                default: () => lang('form.daterange.start_placeholder'),
            },
            endPlaceholder: {
                type: String,
                default: () => lang('form.daterange.end_placeholder'),
            },
            clearable: {
                type: Boolean,
                default: true,
            },
            readOnly: Boolean,
            mondayFirst: Boolean,
        },
        computed: {
            transformedValue() {
                const value = this.value || {};
                return value.start || value.end ? [
                    value.start,
                    value.end,
                ] : null;
            },
            transformedFormat() {
                return this.displayFormat
                    .replace(/D/g, 'd')
                    .replace(/Y/g, 'y');
            },
            pickerOptions() {
                return {
                    firstDayOfWeek: this.mondayFirst ? 1 : 7,
                }
            },
            classes() {
                return {
                    'SharpDateRange--clearable': this.clearable,
                }
            },
        },
        methods: {
            handleChanged(value) {
                const range = value || [];
                this.$emit('input', {
                    start: range[0],
                    end: range[1],
                });
            },
            handleFocused() {
                this.$emit('focus');
            },
            handleBlur() {
                this.$emit('blur');
            },
        },
    }
</script>