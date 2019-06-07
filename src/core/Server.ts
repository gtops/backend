import * as restify from "restify";
import { Request, Response } from "restify";
import * as cors from "restify-cors-middleware";
import { CorsMiddleware } from "restify-cors-middleware";
import { Config } from "../config";
import { IDescriptionENV } from "../config/IDescriptionENV";
import { ACL } from "../middleware/ACL";
import { routes } from "../routes/routes";
import { Tool } from "../tools/Tool";
import { client } from "./Database";
import { databaseInstance } from "./DatabaseInstance";
import { Router } from "./routes/Router";

export class Server {
	private static readonly env: IDescriptionENV = Tool.getEnvironment();
	private static acl: ACL;

	public static async run(server: restify.Server): Promise<void> {
		// await databaseInstance.configure();
		const { port } = Server.env.server;
		Server.acl = new ACL();
		Server.init(server);
		await client.connect();
		const router = new Router(server, routes, Server.acl);
		await router.init();
		server.listen(process.env.PORT || port, Server.listen);
	}

	private static listen(): void {
		const { url } = Server.env.server;
		console.log(`Server is running at: ${ url }`);
	}

	private static init(server: restify.Server): void {
		const CORS: CorsMiddleware = cors({
			origins: Config.cors.originUrls,
			allowHeaders: Config.cors.allowHeaders,
			exposeHeaders: Config.cors.exposeHeaders
		});
		server.use(restify.plugins.acceptParser(server.acceptable));
		server.use(restify.plugins.queryParser());
		server.use(restify.plugins.bodyParser());
		server.use(restify.plugins.authorizationParser());
		server.use(restify.plugins.multipartBodyParser());
		server.use(Server.acl.auth);
		server.use(CORS.actual);
		server.pre(CORS.preflight);
		server.get("/", (request: Request, response: Response) => response.send("It's work"));
	}
}
