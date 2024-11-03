// configure.js
const apiKey = process.env.POKEMON_TCG_API_KEY;

if (!apiKey) {
  console.error("Error: POKEMON_TCG_API_KEY environment variable not set.");
  process.exit(1);
}


module.exports = {
    host: 'https://api.pokemontcg.io/v2',
    apiKey: apiKey,
};