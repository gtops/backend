import { Client, ClientConfig } from "pg";
import { Config } from "../config";

class Database {
	public static configure(): ClientConfig {
		const { host, password, port, username, name } = Config.database;
		return {
			host,
			database: name,
			user: username,
			password,
			port,
			ssl: true
		};
	}
}

export const client = new Client(Database.configure());
