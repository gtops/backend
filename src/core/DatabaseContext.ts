import { Sequelize, SequelizeOptions } from "sequelize-typescript";
import { IDescriptionENV } from "../config/IDescriptionENV";
import { models } from "../models/models";
import { Tool } from "../tools/Tool";

class DatabaseContext {
	private readonly _service: Sequelize;

	public async configure(): Promise<Sequelize> {
		const env: IDescriptionENV = Tool.getEnvironment();
		const options: SequelizeOptions = {
			host: "localhost",
			database: "postgres",
			username: "postgres",
			password: "qwerty123",
			port: 5432,
			dialect: "postgres"
		};
		const services = new Sequelize(options);
		services.addModels(models);
		await services.sync();
		return services;
	}

	public get service(): Sequelize {
		return this._service;
	}
}

export const databaseContext = new DatabaseContext();
