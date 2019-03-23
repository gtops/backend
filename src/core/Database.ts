import { Client, ClientConfig } from "pg";
import { IDescriptionENV } from "../config/IDescriptionENV";
import { Tool } from "../tools/Tool";

class Database {
	public static configure(): ClientConfig {
		const env: IDescriptionENV = Tool.getEnvironment();
		const { host, password, port, username, name, ssl } = env.database;
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
