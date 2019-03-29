import * as restify from "restify";
import { ServerOptions } from "restify";
import { IDescriptionENV } from "./config/IDescriptionENV";
import { Server } from "./core/Server";
import { Tool } from "./tools/Tool";

const env: IDescriptionENV = Tool.getEnvironment();

const SERVER_OPTION: ServerOptions = {
	name: env.server.name
};

const server: restify.Server = restify.createServer(SERVER_OPTION);
export const seed = Server.run(server).catch(console.error);
