import * as restify from "restify";
import { ServerOptions } from "restify";
import { config } from "./config/Config";
import { Server } from "./core/Server";

const SERVER_OPTION: ServerOptions = {
	name: config.server.name
};

const server: restify.Server = restify.createServer(SERVER_OPTION);
export const seed = Server.run(server).catch(console.error);
