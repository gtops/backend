import { Client, ClientConfig } from "pg";
import { Config } from "../config";

class Database {
	public static configure(): ClientConfig {
		const { host, password, port, username, name, ssl } = Config.database;
		return {
			host,
			database: name,
			user: username,
			password,
			port,
			ssl
		};
	}
}

export const client = new Client(Database.configure());
