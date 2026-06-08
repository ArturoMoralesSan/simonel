<template>
    <button
        class="btn"
        :class="{ 'btn--loading' : $parent.isSubmitting }"
        :disabled="$parent.isSubmitting"
        type="submit"
    >
        <slot>
            Enviar
        </slot>
    </button>
</template>
