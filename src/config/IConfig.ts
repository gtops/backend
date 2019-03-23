import { IDescriptionENV } from "./IDescriptionENV";

export interface IConfig {
	production: IDescriptionENV;
	development: IDescriptionENV;
	cors: {
		originUrls: string[];
		allowHeaders: string[];
		exposeHeaders: string[];
	};
	jwt: {
		secret: string;
		tokenTimeLive: string;
	};
	other: {
		passwordCrypt: string;
		uidMask: string;
	};
}
