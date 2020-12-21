<template>
    <modal @close="close">
        <div slot="header">
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title">Create Monster</h5>
        </div>

        <div slot="body">
            <form action="/createNewMonster" method="POST" class="form-horizontal">
                    <div class="input-group mb-3">
                        <input id="monsterName" type="text" name="name" maxlength="20" class="form-control" v-model="monsterName" placeholder="Enter a name..." value="">
                        <div class="input-group-append">
                            <button class="btn btn-success" @click="setRandomName" type="button">Generate Random Name!!</button>
                        </div>
                    </div>             
                <div class="form-group"> 
                    <div class="btn-group btn-group-toggle float-none d-flex" data-toggle="buttons">
                        <label class="btn btn-info active">
                            <input type="radio" name="level" value="basic" id="basic" autocomplete="off" checked> 
                            <h5>Basic</h5>
                            <small>Open to everyone</small>
                        </label>
                        <label class="btn btn-info">
                            <input type="radio" name="level" value="standard" id="standard" autocomplete="off"> 
                            <h5>Standard</h5>
                            <small>Registered users only</small>
                        </label>
                        <label v-if="user_is_vip" class="btn btn-info">
                            <input type="radio" name="level" value="pro" id="pro" autocomplete="off"> 
                            <h5>Pro</h5>
                            <small>Advanced users only</small>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch mb-2">
                        <input type="checkbox" name="nsfw" class="custom-control-input" id="nsfw">
                        <label class="custom-control-label" for="nsfw">
                            NSFW
                            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Not Safe For Work. (i.e. For adults only)"></i>
                        </label>
                    </div>
                </div>
                <div class="form-group"> 
                    <button id="createMonster" type="submit" class="btn btn-success form-control" :disabled="monsterName == ''">
                        Create Monster
                    </button>
                </div>    
            </form>
        </div>

        <div slot="footer">
            <button type="button" class="btn btn-default" @click="$emit('close')">Close</button>
        </div>
    </modal>
</template>

<script>
    import modal from './Modal' ;

    export default {
        props: {
           user_is_vip: Number
        },
        components: {
            modal
        },
        data() {
            return {
                monsterName:'',
                adjectives: [
                    'bucktoothed','apple-sized','oxtongue','bare-assed','botchy','football-shaped',
                    'sourpuss','Yugoslavian','tunnel','macrame','mealymouthed','asshole','Slovenian',
                    'openmouthed','liver-colored','nubby','Yankee-Doodle','peeper','aboveground','beatnik',
                    'beat-up','spaced-out','wise guy','whiskery','charmed','reflexive','junky','creepy',
                    'off the cuff','anticoagulant','outsized','fanged','jellyfish','yellow-gray','bratty',
                    'droopy','achy','werewolf','coin-operated','openhanded','fattish','eyeless','rice paper',
                    'honey-colored','punk','redheaded','peeling','ham-handed','underground','ratty','mismatched',
                    'crew cut','rust-colored','bleary','horn-rimmed','looney','heavyset','honest-to-goodness',
                    'unmelted','light-skinned','jaybird','contortionist','boring','brown-haired','lipstick',
                    'singsong','upside-down','catatonic','frizzy','lambskin','screwy','yelling','panicked',
                    'impermanent','soupy','night school','transistor','queasy','hypnotized','stinky','uppercut',
                    'uncluttered','groovy','chubby','emotionless','dark-haired','skeletal','cold turkey','redneck',
                    'gumming','tatty','living dead','doughy','armpit','junkyard','henchman','cobwebby','nasty',
                    'smelling','scraping','cardboard','locker','drop down','redhead','mixed-up','Weird','East River',
                    'blemished','stinking','halfhearted','barreled','grumpy','waxy','honeycombed','fizzing','shotgun',
                    'unwrinkled','hi-fi','clipped','raunchy','rotten','wormy','larger-than-life','chiseled','pet',
                    'headless','bugged','decapitated','starving','forgettable','adolescent','Goliath','ravenous',
                    'dislocated','dead','skinny','squatting','close','huffy','heated up','Island','biting','boozy',
                    'slithering','killer','teacup','coincidental','overdressed','Yiddish','sticking out','squeamish',
                    'uncombed','neon','drop dead','bouncy','razor','tacky','slowing','hypnotist','encyclopedic',
                    'bothered','reset','Amish','wilting','mannish','Halloween','spattering','universalist','sneaky',
                    'bleeding','undercut','undercover','paisley','canine','crescent-shaped','kinky','stabbing','unmentionable',
                    'barefoot','ventriloquist','dead set','stupid','filthy','weakling','muggy','licking','ghoulish',
                    'drowsing','bald','dead end','hurting','interlaced','sympathizer','smiley','smallish','sticky',
                    'clawed','lousy','necromancer','leftover','hobbit','quitter','sleeping','backstage','drunk','pretty',
                    'kosher','ankle-deep','spraying','missing','nauseated','mistreated','zombie','longish','unreadable',
                    'spiky','hysteric','light-blue','moon','unwanted','devil','staring','stacked','thumping','peaked',
                    'beefy','undocumented','open-minded','millennial','hippie','whacked','light-headed','rolling','Disneyland',
                    'oversized','amble','Blessed','repetitive','ambrosial','open-eyed','drugged','chummy','coyote',
                    'personable','elbowing','reddish-brown','vacationing','incandescent','allergic','one-on-one',
                    'Samaritan','sticking','heart-shaped','ranting','classy','movie star','sweating','lucky',
                    'believable','neighborhood','hypodermic','unreliable','snow-capped','blaring','three-dimensional','snaky',
                    'king','robotic','underweight','handsome','crazy','hairless','gothic','stolen','lacey','french',
                    'light-hearted','underdressed', 'tight','rum-soaked','cheesy','spicy','overexcited','fuzzy',
                    'fluffy','ginger','pumpkin-spiced','twitching','slimy','all-American','Mr'
                ],
                nouns: [
                    'Accountant','Apple','Air-conditioner','Airport','Ambulance','Aircraft','Apartment','Arrow',
                    'Antlers','Apro','Alligator','Architect','Ankle','Armchair','Aunt','Ball','Baguette','Bermudas',
                    'Beans','Balloon','Bear','Blouse','Bed','Bow','Bread','Black','Board','Bones','Bill',
                    'Bitterness','Boxers','Belt','Brain','Buffalo','Bird','Baby','Book','Back','Butter',
                    'Bulb','Buckles','Bat','Bank','Bag','Bra','Boots','Blazer','Bikini','Bookcase',
                    'Bookstore','Bus stop','Brass','Brother','Boy','Blender','Bucket','Bakery','Bow',
                    'Bridge','Boat','Bouncer','Baby','Car','Car-wash','Cow','Cap','Cooker','Cheeks',
                    'Cheese','Chiropractor','Cellist','Carpet','Crow','Crocodile','Clown','Crocs',
                    'Crest','Chest','Chair','Candy','Cabinet','Cat','Coffee','Children','Cookware','Chaise longue',
                    'Chicken','Casino','Cabin','Castle','Church','Café','Cinema','Choker','Cravat','Cane','Costume',
                    'Cardigan','Chocolate','Crib','Couch','Cello','Cashier','Composer','Cave','Country','Computer',
                    'Canoe','Clock','Charlie','Dog','Deer','Donkey','Desk','Desktop','Dress','Dolphin','Doctor',
                    'Dentist','Dream','Dog-groomer','Drum','Dresser','Designer','Detective','Daughter','Duck',
                    'Egg','Elephant','Earrings','Ears','Englishman','Eyes','Estate','Finger','Fox','Flamingo',
                    'Frock','Frog','Fan','Freezer','Fish','Film','Foot','Flag','Factory','Feather','Frappé','Footballer',
                    'Father','Farm','Flip-flops','Forest','Flower','Fruit','Fork','Grapes','Goat','Gown','Garlic','Ginger','Giraffe',
                    'Gauva','Grains','Gas station','Garage','Gloves','Glasses','Gift','Galaxy','Goth','Goblin','Guitar','Grandmother',
                    'Grandfather','Groundhog','Governor','Girl','Guest','Hamburger','Hand','Head','Hair','Heart','House','Horse',
                    'Hen','Horn','Hat','Hammer','Hostel','Hospital','Hotel','Hole','Heels','Herbs','Host','Jacket','Jersey',
                    'Jewelry','Jaw','Jumper','Judge','Juicer','Keyboard','Kid','Kangaroo','Koala','Knife','Lemon','Lion',
                    'Leggings','Leg','Laptop','Library','Lamb','Llama','London','Lips','Lung','Lighter','Luggage','Lamp','Lawyer',
                    'Manequin','Mouse','Monkey','Mouth','Mango','Mobile','Milk','Morgue','Music','Mirror','Musician','Moth','Mother','Man','Model',
                    'Mall','Museum','Market','Moonlight','Medicine','Microscope','Newspaper','Ninja','Nose','Notebook','Neck',
                    'Noodles','Nurse','Necklace','Noise','Ocean','Ostrich','Oil','Orange','Onion','Oven','Owl','Paper',
                    'Panda','Pants','Palm','Pasta','Pumpkin','Pharmacist','Potato','Parfume','Panther','Pad','Pencil',
                    'Pipe','Police','Pen','Pharmacy','Petrol station','Police station','Parrot','Plane','Pigeon','Phone',
                    'Peacock','Pencil','Pig','Pouch','Pagoda','Pyramid','Purse','Pancake','Popcorn','Piano','Physician',
                    'Photographer','Professor','Painter','Park','Plant','Parfume','Radio','Razor','Ribs','Rainbow','Ring',
                    'Rabbit','Rice','Refrigerator','Remote','Restaurant','Road','Surgeon','Scale','Shampoo','Sink',
                    'Salt','Sausages','Shark','Sandals','Shoulder','Spoon','Soap','Sand','Sheep','Sari','Stomach','Stairs','Soup',
                    'Shoes','Scissors','Sparrow','Shirt','Suitcase','Stove','Stairs','Snowman','Shower','Swan','Suit',
                    'Sweater','Smoke','Skirt','Sofa','Socks','Stadium','Skyscraper','School','Sunglasses','Sandals','Slippers',
                    'Shorts','Sandwich','Strawberry','Spaghetti','Shrimp','Saxophone','Sister','Son','Singer','Senator',
                    'Street','Squirrel','Supermarket','Swimming pool','Star','Sky','Sun','Spoon','Ship','Smile','Table','Turkey',
                    'Tie','Toes','Truck','Train','Taxi','Tiger','Trousers','Tongue','Television','Teacher','Turtle','Tablet',
                    'Train station','Toothpaste','Tail','Theater','Trench coat','Tea','Tomato','Teen','Tunnel','Temple',
                    'Town','Toothbrush','Tree','Toy','Tissue','Telephone','Underwear','Uncle','Underpants','Umbrella','Vest','Voice',
                    'Veterinarian','Villa','Violin','Village','Vehicle','Vase','Wallet','Wolf','Waist','Wrist','Water melon',
                    'Whale','Water','Wings','Whisker','Watch','Woman','Washing machine','Wheelchair','Waiter','Wound','Yacht',
                    'Zebra','Zoo'
                ],
                prefixes: [
                   'I can\'t believe it\'s not', 'Look- it\'s', 'Here comes', 'Looks like', 'Where\'s the', 'Very', 'Extremely',
                   'Fairly', 'My', 'My very own','Old','Rather','Literally', 'Yes, I\'m a','Time for'
                ],
                suffixes: [
                    'at midnight','uncensored','with bells on','uncovered','Ltd','and co', 'at last',
                    'in 2050', 'at last', '(FREE!!)', 'only $4.99', '- on sale now','- the hero we deserve', '- my hero',
                    'for tea','- recommended by dentists','(as seen on tv)', 'TM', 'of doom', 'of death','O\'clock'
                ]
            }
        },
        mounted() {
            console.log('Component mounted.');
            document.getElementById('monsterName').focus();
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
        },
        methods: { 
            close: function() {
                this.$emit('close')
            },
            setRandomName:function(){
                var name = this.generateRandomName();
                this.monsterName = this.capitaliseFirstLetter(name);
            },
            generateRandomName: function(){
                var name;
                var adjective = this.rand(this.adjectives);
                var noun = this.rand(this.nouns);
                var prefix = this.rand(this.prefixes);
                var suffix = this.rand(this.suffixes);
                name = prefix + ' ' + adjective + ' ' + noun  + ' ' + suffix;

                if (name.length > 20){ 
                    var num = Math.random();
                    if (num < 0.8){
                        name = adjective + ' ' + noun;
                    } else if (num < 0.9){
                        name = prefix + ' ' + adjective + ' ' + noun;
                    } else{
                        name = noun + ' ' + suffix;
                    }
                }

                if (name.length > 20){ 
                    //try again
                    name = this.generateRandomName();
                }

                return name;
            },
            rand: function(items){
                return items[Math.floor(Math.random() * items.length)];
            },
            capitaliseFirstLetter: function(s) {
                if (typeof s !== 'string') return ''
                return s.charAt(0).toUpperCase() + s.slice(1)
            }    
        }
    }
</script>
<style scoped>
    .form-group label {
        clear:both;
        float:left;
        vertical-align: top;
    }
    .form-group div {
        float:left;
        padding-bottom:5px;
    }
    #sendButton{
        margin-top:10px;
    }

    .btn-info:not(.active){
        background-color:#DDEDFA!important;
    }
    .btn-info:not(.active):hover{
        color:#C0C0C0;
    }
    #nsfw{
        margin-left:3px!important;
    }
</style>