import restify, { ServerOptions } from "restify";
import { Config } from "./config/Config";
import { Server } from "./core/Server";

const SERVER_OPTION: ServerOptions = {
	name: Config.server.name
};

const server: restify.Server = restify.createServer(SERVER_OPTION);
export const seed = Server.run(server);
