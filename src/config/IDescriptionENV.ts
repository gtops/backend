export interface IDescriptionENV {
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
		ssl?: boolean;
	};
}
