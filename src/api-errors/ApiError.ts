import { EHttpStatus } from "./EHttpStatus";

export class ApiError extends Error {
	private status: number;
	private title: string;
	private code: number;

	constructor(status: number, title: string, code: number) {
		super(title);
		this.status = status;
		this.title = title;
		this.code = code;
	}
}

export const errors = {
	InvalidToken: new ApiError(EHttpStatus.UNAUTHORIZED, "Токен истек или не валиден", 1),
	PermissionError: new ApiError(EHttpStatus.FORBIDDEN, "Недостаточно прав", 2),
	TokenNotFound: new ApiError(EHttpStatus.NOT_FOUND, "Токе не найден", 3),

	IncorrectUid: new ApiError(EHttpStatus.BAD, "Некорректный uid пользователя", 100),
	NotFoundParticipantUid: new ApiError(EHttpStatus.NOT_FOUND, "Участника с данным uid не существует", 101),

	UserNotFound: new ApiError(EHttpStatus.NOT_FOUND, "Пользователь не найден", 130),
	IncorrectPassword: new ApiError(EHttpStatus.BAD, "Неверный пароль", 131),
	UserAlreadyExist: new ApiError(EHttpStatus.BAD, "Пользователь с таким логином или эмайлом существует", 132),

	TrialsNotFound: new ApiError(EHttpStatus.NOT_FOUND, "Соревнований не найдено", 160),

	UnknownController: new ApiError(EHttpStatus.INTERNAL, "Задан неизвестный контроллер", 500),
	NotAssignedRouteMethod: new ApiError(EHttpStatus.INTERNAL, "Неопределённый метод роута", 501),
	UnknownRouteHandle: new ApiError(EHttpStatus.INTERNAL, "Неизвестный обработчик маршрута", 502),
	ServerError: new ApiError(EHttpStatus.INTERNAL, "Произошла ошибка на сервере", 503)
};
