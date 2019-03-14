import * as restify from "restify";
import { ServerOptions } from "restify";
import { Config } from "./config";
import { Server } from "./core/Server";

const SERVER_OPTION: ServerOptions = {
	name: Config.server.name
};

const server: restify.Server = restify.createServer(SERVER_OPTION);
export const seed = Server.run(server);
