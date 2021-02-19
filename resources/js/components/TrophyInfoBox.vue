<template>
    <div class="container">
        <div class="card w-100 border-0">
            <div class="card-header">
                {{ trophyStyleUcFirst }} Trophy Details
                <i class="fa fa-times pull-right close" @click="$emit('close')" title="Close"></i>
            </div>
            <div class="card-body">
                <ul v-if="trophyInfo.length" class="mb-2">
                    <li v-for= "trophy in trophyInfo" :key="trophy.id" :title="'Awarded ' + trophy.created_at_date"
                    v-html = "getDescription(trophy)">
                    </li>
                </ul>
                <div v-else class="ml-3 mb-2"><i>None</i></div>
                <a href="/trophies" class="ml-3">More info...</a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            trophyInfo: Array,
            trophyStyle: String,
        },
        methods: {
            close: function() {
                this.$emit('close')
            },
            getDescription: function(trophy) {
                var html = trophy.trophy_type ? trophy.trophy_type.description : trophy.default_description_html;
                const parser = new DOMParser();
                const elem = parser.parseFromString(html, 'text/html');

                return elem.body.innerText;
            }
        },
        computed: {
            trophyStyleUcFirst: function()
            {
                var str = this.trophyStyle;
                return str.charAt(0).toUpperCase() + str.slice(1);
            }
        },
        mounted() {
            console.log('Component mounted.');
        }
    }
</script>
<style scoped>
    .container{
        width: 100%!important;
        padding: 0px;
    }
    .card-header{
        padding: 0.15rem 1.25rem;
        font-size: 14px;
    }
    .card-body{
        padding: 0.25rem;
        margin-left: -10px;
        white-space:normal;
    }
    .close{
        font-size:12px;
        padding:5px;
        cursor:pointer;
    }
</style>
