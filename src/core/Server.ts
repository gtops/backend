import * as restify from "restify";
import { Request, Response } from "restify";
import * as cors from "restify-cors-middleware";
import { CorsMiddleware } from "restify-cors-middleware";
import { Config } from "../config";
import { ParticipantController } from "../controller";

export class Server {
	public static async run(server: restify.Server) {
		const { port } = Config.server;
		Server.init(server);
		Server.initRoutes(server);
		server.listen(process.env.PORT || port, Server.listen);
	}

	private static listen() {
		const { url } = Config.server;
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
		server.get("/", (request: Request, response: Response) => response.send("Pashalka dlya jahi"));
	}

	private static initRoutes(server: restify.Server) {
		const participantController = new ParticipantController();

		server.get("/api/v1/participant", participantController.getDataParticipant);
	}
}
