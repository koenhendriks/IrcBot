<?php
/**
 * Random.class.php.
 * User: koen
 * Date: 4-12-14
 * Time: 12:30
 */

class Random {

    /**
     * @var array with random sentences
     */
    public $randoms = array("​I am so blue I'm greener than purple.​",
        "​I stepped on a Corn Flake, now I'm a Cereal Killer​",
        "​Banana error.​",
        "​Llamas eat sexy paper clips​",
        "​Look, a distraction!​",
        "​Everyday a grape licks a friendly cow​",
        "​Screw world peace, I want a pony​",
        "​A hotdog on a bridge​",
        "​My nose is a communist.​",
        "​Marry Poppins killed a shopping trolley.​",
        "​Oh no, you're one of THEM!!!!​",
        "​On a scale from one to ten what is your favourite colour of the alphabet.​",
        "​When life gives you lemons, chuck them at people you hate​",
        "​Friendly insects eat pink pineapples, while looking at your mum​",
        "​A fuzzy snake ate the clouds​",
        "​Don't tell anybody, but I'm dead.​",
        "​The sparkly lamp ate a pillow then punched Larry.​",
        "​No sheep is quite as crooked as a bed​",
        "​Spongebob ate Britney Spears​",
        "​Isn't that against the rules? Screw the rules I have green hair!​",
        "​Are you a fan of delicious flavor?​",
        "​There's a purple mushroom in my backyard, screaming Taco's!​",
        "​Thank you for noticing this list, your noticing has been noticed​",
        "​Call the pineapple if you feel happy​",
        "​A Hairy window broke a silly pineapple with a Blue fridge​",
        "​Cheese grader shaved my butt skin off​",
        "​Rainbows fly under the world.​",
        "​Drink my blood, it'll make you sick.​",
        "​I am not a hoarder, I am a lover of things.​",
        "​Flying dogs like cheese.​",
        "​Nom nom nom nom nom!​",
        "​I like pie​",
        "​Bob ate a sandwich under a hungry tree.​",
        "​Banana crap shake pillow lock.​",
        "​There's an ugly chimpanzee in my bathroom and won't stop yelling the f word​",
        "​You know farts smell like radishes roasting on an open fire.​",
        "​Yo mama is so ugly she made onions cry​",
        "​Woman in the attic​",
        "​Donkey Poo Can Make You Chorophobic​",
        "​Eat my sister.​",
        "​House fires are cold.​",
        "​Snow feels so warm and sensual.​",
        "​Elizabeth Taylor drunk bigfoot taco​",
        "​Otis smells like fungus.​",
        "​Fuzzy bucket of lies.​",
        "​Don't touch my crayons, they can smell glue​",
        "​Damn the rain is wet again​",
        "​Man actually evolved into ape​",
        "​When does the 5 o'clock news start​",
        "​I'm in is bag.​",
        "​Choosing your socks by how much glue they have on them isn't right.​",
        "​Eat my shorts!!​",
        "​Your ugliness made me bang my head on a big rock​",
        "​I like Trains​",
        "​A mutant race car ran over the Pope​",
        "​I found Santa drunk on my table when I went to see my presents​",
        "​Shhh, the cheese may tell your grandma​",
        "​Did you ever notice that pineapples never wear bathrobes?​",
        "​A Zebra licked a DVD​",
        "​I got a special bacon, then you throw me text airplanes in the censored face.​",
        "​I have a banana phone that really works​",
        "​Bigfoot crapped on a purple apple then ate it​",
        "​I saw horses puke.​",
        "​phsychedelic sandwich​",
        "​There's a big blue muffin in the backyard and it wants pie with cheeze​",
        "​Dumb apple​",
        "​If there was a monkey in your suitcase what will you do?​",
        "​Don't Touch My Filatis!​",
        "​I like squirrels​",
        "​I'm secretly a ninja llama, but shh don't tell anyone!​",
        "​If I am purple the sun is called a blue taco​",
        "​Giraffes Eat Clothes​",
        "​I'll give you 100 dollars for a nickel​",
        "​I said don't enter the rabbit hole. Now you have the salad.​",
        "​John Cuzack ate the green zebra while the lamp stared out the fuzzy window​",
        "​My sweat smell like armpits!​",
        "​So when I changed the light bulb, the frog screamed blue muffins.​",
        "​Spread nutella not butter​",
        "​If the gorilla knocks me off the roof shoot the dog!​",
        "​Monkeys jumping are big.​",
        "​Oh my god I love you we're going to be together forever and ever and ever​",
        "​I watch you sleep​",
        "​She could be the magic woman that run through your dreams​",
        "​Imma go to yo house and be the stranger in yo bed.​",
        "​There's no business like snow business.​");

    /**
     * Gets a random sentence from given array.
     * @return mixed
     */
    public function getSentence(){
        return $this->randoms[rand(0, (count($this->randoms)-1))];
    }



} 