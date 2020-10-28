<template>
    <div>
        <form method="POST" class="form-horizontal">
            <div class="form-group">
                <div class="custom-control custom-switch mb-2">
                    <input type="checkbox" name="completeEmail" @change="toggleEmailOnComplete()" :checked="allowMonsterEmails" class="custom-control-input" id="completeEmail">
                    <label class="custom-control-label" for="completeEmail">
                        Email me when someone comments on my monster
                    </label>
                </div>
            </div>
            <div class="form-group"> 
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <button id="saveSettings" type="button" @click="location.href='/home'" class="btn btn-info form-control btn-block">
                                Return to Lobby
                            </button>
                        </div>
                        <div class="col-md-6 col-12">
                            <button id="saveSettings" type="button" @click="save()" class="btn btn-success form-control btn-block">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>    
        </form>
    </div>
</template>

<script>

    export default {
        props: {
            allowMonsterEmails: Boolean
        },
        components: {
           
        },
        data() {
            return {
                currentAllowMonsterEmails : this.allowMonsterEmails
            }
        },
        mounted() {
            console.log('Component mounted.');
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
        },
        methods: { 
            toggleEmailOnComplete: function() {
                this.currentAllowMonsterEmails = !this.currentAllowMonsterEmails;
            },
            save: function() {
                axios.post('/settings/save', { 
                    allow_monster_emails: (this.currentAllowMonsterEmails ? 1 : 0)              
                })
                .then((response) => {
                    window.location.href='/home';
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        }
    }
</script>
<style scoped>

    .btn-info:not(.active){
        background-color:#DDEDFA!important;
    }
    .btn-info:not(.active):hover{
        color:#C0C0C0;
    }
</style>