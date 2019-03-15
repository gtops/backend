export interface IConfig {
	server: {
		name: string;
		port: number;
		url: string;
	};
	database: {
		host: string;
		name: string;
		username: string;
		password: string;
		port: number;
		ssl: boolean;
	};
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
