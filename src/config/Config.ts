import { get, pickBy } from "lodash";
import * as ConfigJson from "../../config.json";
import { IConfig } from "./IConfig";

export class Config {
	public static getConfig(): IConfig {
		const envServerData = get(ConfigJson, process.env.NODE_ENV as string);
		const blackList = ["production", "development", "test", "default"];
		const otherData = pickBy(ConfigJson, (value, key) => !blackList.includes(key));

		return {
			...envServerData,
			...otherData
		};
	}
}

export const config = Config.getConfig();
