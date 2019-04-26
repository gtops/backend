import { IDescriptionENV } from "./IDescriptionENV";

export interface IConfig {
	projectName: string;
	production: IDescriptionENV;
	development: IDescriptionENV;
	test: IDescriptionENV;
	cors: {
		originUrls: string[];
		allowHeaders: string[];
		exposeHeaders: string[];
	};
	jwt: {
		secret: string;
		tokenTimeLive: string;
	};
	jwtInviteLink: {
		secret: string;
		tokenTimeLive: string;
	};
	addressFrontendServer: {
		url: string;
		port?: number;
	};
	other: {
		uidMask: string; // Only * (number) and - (symbol)
	};
	email: {
		addressFrom: string;
		SMTPData: {
			host: string;
			port: number;
			auth: {
				user: string;
				pass: string;
			}
		}
	};

}
