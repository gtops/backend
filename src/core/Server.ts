import restify from "restify";
import { Config } from "../config/Config";

export class Server {
	public static async run(server: restify.Server) {
		const { port } = Config.server;
		Server.init(server);
		server.listen(port, Server.listen);
	}

	private static listen() {
		const { url } = Config.server;
		console.log(`Server is running at: ${url}`);
	}

	private static init(server: restify.Server): void {
		server.use(restify.plugins.acceptParser(server.acceptable));
		server.use(restify.plugins.queryParser());
		server.use(restify.plugins.bodyParser());
		server.use(restify.plugins.authorizationParser());
		server.use(restify.plugins.multipartBodyParser());
	}
}
