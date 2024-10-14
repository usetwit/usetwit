<script setup>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
</script>

<template>
<input type="hidden" name="_token" :value="csrfToken"/>
</template>
