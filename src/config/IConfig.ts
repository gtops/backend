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
	};
	cors: {
		originUrls: string[];
		allowHeaders: string[];
		exposeHeaders: string[];
	};
}
