import { Client, ClientConfig } from "pg";
import { config } from "../config/Config";

class Database {
	public static configure(): ClientConfig {
		const { host, password, port, username, name, ssl } = config.database;
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
