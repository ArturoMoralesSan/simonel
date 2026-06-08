<template>
    <a target="_blank" :href="$root.path + url + branchid + 
        '?start_date=' + startdate + '&end_date=' + enddate">
        PDF
    </a>
</template>
<script>
    export default {
        props: {
            branchid: {
                type:Number,
                required: true
            },
            startdate: {
                type:String,
                required: true
            },
            enddate: {
                type:String,
                required: true
            },
            url : {
                type: String,
                required:true
            }
        },
    };
</script>