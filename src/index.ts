import "module-alias/register";

import { config } from "@config/Config";
import { Server } from "@core/Server";
import * as restify from "restify";
import { ServerOptions } from "restify";

const SERVER_OPTION: ServerOptions = {
	name: config.server.name
};

const server: restify.Server = restify.createServer(SERVER_OPTION);
export const seed = Server.run(server).catch(console.error);
