import { Sequelize, SequelizeOptions } from "sequelize-typescript";
import { IDescriptionENV } from "../config/IDescriptionENV";
import { models } from "../models/models";
import { Tool } from "../tools/Tool";

class DatabaseContext {
	private readonly _service: Sequelize;

	public constructor() {
		const env: IDescriptionENV = Tool.getEnvironment();
		// TODO: создай локальную бд пока что
		const options: SequelizeOptions = {
			host: "localhost",
			database: "postgres",
			username: "postgres",
			password: "qwerty123",
			port: 5432,
			dialect: "postgres",
			models
		};
		this._service = new Sequelize(options);
	}

	public get service(): Sequelize {
		return this._service;
	}
}

export const client = new DatabaseContext();
export const dbServices = client.service;
