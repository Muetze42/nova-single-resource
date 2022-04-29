<template>
    <div :class="`text-${field.textAlign}`" class="single-resource-index-field">
        <Icon v-if="field.icon" :type="field.icon"/>
        <i v-if="field.faIcon" :class="field.faIcon"></i>
        <template v-if="hasValue">
            <div v-if="field.asHtml" @click.stop v-html="field.value"></div>
            <span v-else class="whitespace-nowrap">{{ field.value }}</span>
        </template>
        <p v-else>&mdash;</p>
    </div>
</template>

<script>
export default {
    props: ['resourceName', 'field'],

    computed: {
        /**
         * Determine if the field has a value other than null.
         */
        hasValue() {
            return this.field.value !== null
        },
    },
}
</script>
