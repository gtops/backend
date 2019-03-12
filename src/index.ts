import restify from "restify";

const app = restify.createServer();

app.listen(5000, () => {
    console.log(`Server is listening on port ${5000}`);
});
